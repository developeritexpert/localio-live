@extends('admin_layout.master')
@section('content')
    <style>
        .ck-editor__editable_inline {
            min-height: 140px;
        }
        .sub-section-item .ck-editor__editable_inline {
            min-height: 100px;
        }
        .ck-content strong,
        .ck-content b,
        .ck-editor__editable strong,
        .ck-editor__editable b,
        .ck-editor__editable_inline strong,
        .ck-editor__editable_inline b {
            font-weight: 700 !important;
        }
        .ck-content em,
        .ck-content i,
        .ck-editor__editable em,
        .ck-editor__editable i,
        .ck-editor__editable_inline em,
        .ck-editor__editable_inline i {
            font-style: italic !important;
        }
    </style>
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">
                    {{ isset($category_data) ? 'Update Category' : 'Add Category' }}
                </h4>
            </div>
            <div>
            </div>
        </div>
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('add-category-process') }}" class="form-validate" novalidate="novalidate" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="category_id" value="{{isset($category_data)?$category_data['id']:''}}" />
                    <div class="row g-gs">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="name">Name</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{isset($category_data)?$category_data['name']:old('name') }}" />
                                </div>
                                @error('name')
                                <div class="error text-danger">{{ $message }}</div>
                            @enderror
                            </div>
                        </div>
                                                <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="comparison_name">Category name for comparison</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="comparison_name" name="comparison_name"
                                        value="{{ isset($category_data) ? ($category_data['comparison_name'] ?? '') : old('comparison_name') }}" 
                                        placeholder="e.g. Shared hosting providers" />
                                </div>
                                <small class="text-muted d-block mt-1">Used to generate feature page headlines (e.g. <i>Shared hosting providers with noise cancellation</i>). Defaults to Category Name if left blank.</small>
                                <small class="text-primary d-block mt-1"><strong>Dynamic Variables Supported:</strong> You can use <code>[year]</code>, <code>[country1]</code>, and <code>[country2]</code>. Example: <em>Web hosting comparison [year] [country2]</em></small>
                                @error('comparison_name')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="see_all_text">"See All" Link Text</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="see_all_text" name="see_all_text"
                                        value="{{ isset($category_data) ? ($category_data['see_all_text'] ?? '') : old('see_all_text') }}" 
                                        placeholder="e.g. See all {{ $category_data['name'] ?? 'Categories' }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Used on the Top Rated Products block (e.g. <i>See all CRM software</i>). Defaults to "See all [Category Name]" if left blank.</small>
                                @error('see_all_text')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="page_title">Page Title (H1 Heading)</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="page_title" name="page_title"
                                        value="{{ isset($category_data) ? ($category_data['page_title'] ?? '') : old('page_title') }}" 
                                        placeholder="e.g. Shared hosting comparison (defaults to Category Name if left blank)" />
                                </div>
                                <small class="text-muted d-block mt-1">Defines the main title/H1 displayed on the category or subcategory page. Defaults to Name if empty.</small>
                                <small class="text-primary d-block mt-1"><strong>Dynamic Variables Supported:</strong> You can use <code>[year]</code>, <code>[country1]</code>, and <code>[country2]</code>. Example: <em>Web hosting comparison [year] [country2]</em></small>
                                @error('page_title')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="page_title_addition">Page Title (H1 Heading) addition (no translate)</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="page_title_addition" name="page_title_addition"
                                        value="{{ isset($category_data) ? ($category_data['category']['page_title_addition'] ?? '') : old('page_title_addition') }}" 
                                        placeholder="e.g. [country2] ([year])" />
                                </div>
                                <small class="text-muted d-block mt-1">This text is appended to the H1 title and is never translated. Perfect for dynamic variables like <code>[country2] ([year])</code>.</small>
                                @error('page_title_addition')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="title">Title (Most Popular Section)</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="{{ isset($category_data) ? ($category_data['title'] ?? '') : old('title') }}" />
                                </div>
                                @error('title')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="comparison_slug">Comparison Slug (SEO Route)</label>
                                <div class="form-control-wrap">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="comparison_slug" name="comparison_slug"
                                            value="{{ isset($category_data) ? ($category_data['comparison_slug'] ?? '') : old('comparison_slug') }}" 
                                            placeholder="e.g. business-name-comparison" readonly />
                                        <button class="btn btn-light" type="button" id="edit_comparison_slug_btn" title="Edit Comparison Slug">
                                            <em class="icon ni ni-edit"></em>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-1">Auto-assigned as <code>[category-name]-comparison</code>. Click the pencil icon to edit manually.</small>
                                @error('comparison_slug')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="description">Description</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control rich-editor" id="description" name="description" rows="5" placeholder="Enter category description">{!! isset($category_data) ? $category_data['description'] : old('description') !!}</textarea>
                                </div>
                                @error('description')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="top_rated_image">Top-Rated SVG Graphic (300x100)</label>
                                <div class="form-control-wrap">
                                    <div class="form-file">
                                        <input type="file" class="form-file-input" id="top_rated_image" name="top_rated_image" accept="image/svg+xml">
                                        <label class="form-file-label" for="top_rated_image">Choose SVG file</label>
                                    </div>
                                    @if(isset($category) && $category->top_rated_image)
                                        <div class="mt-2">
                                            <span class="badge bg-outline-info">Current graphic uploaded</span>
                                            <!-- <img src="{{ isset($category->topRatedImageMedia) ? asset($category->topRatedImageMedia->dir_path . '/' . $category->topRatedImageMedia->file_name) : '' }}" alt="Graphic" class="mt-1" style="max-height: 50px; display: block;"> -->
                                        </div>
                                    @endif
                                </div>
                                <small class="text-muted d-block mt-1">SVG illustration displayed on the right side of the hero section for this category.</small>
                                @error('top_rated_image')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="top_rated_listing_headline">Top-Rated Listing Headline (H2)</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="top_rated_listing_headline" name="top_rated_listing_headline"
                                        value="{{ isset($category_data) ? ($category_data['top_rated_listing_headline'] ?? '') : old('top_rated_listing_headline') }}"
                                        placeholder="e.g. Best web hosting services">
                                </div>
                                <small class="text-muted d-block mt-1">Headline displayed above the top-rated listings on this category's page. Defaults to "Best [Category Name] services".</small>
                                <small class="text-primary d-block mt-1"><strong>Dynamic Variables Supported:</strong> You can use <code>[year]</code>, <code>[country1]</code>, and <code>[country2]</code>. Example: <em>Best Web hosting [year] [country2]</em></small>
                                @error('top_rated_listing_headline')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="top_rated_listing_headline_addition">Top-Rated Listing Headline (H2) addition (no translate)</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="top_rated_listing_headline_addition" name="top_rated_listing_headline_addition"
                                        value="{{ isset($category_data) ? ($category_data['category']['top_rated_listing_headline_addition'] ?? '') : old('top_rated_listing_headline_addition') }}"
                                        placeholder="e.g. [country2] ([year])">
                                </div>
                                <small class="text-muted d-block mt-1">This text is appended to the Top-Rated Headline and is never translated.</small>
                                @error('top_rated_listing_headline_addition')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="top_rated_listing_subtext">Top-Rated Listing Subtext</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control rich-editor" id="top_rated_listing_subtext" name="top_rated_listing_subtext" rows="3" placeholder="Enter subtext displayed below the H2 headline on the Top Rated page">{{ isset($category_data) ? ($category_data['top_rated_listing_subtext'] ?? '') : old('top_rated_listing_subtext') }}</textarea>
                                </div>
                                <small class="text-muted d-block mt-1">Short text placed immediately below the Top-Rated Listing Headline.</small>
                                @error('top_rated_listing_subtext')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="compare_alternatives_subtext">Compare Alternatives Subtext</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control rich-editor" id="compare_alternatives_subtext" name="compare_alternatives_subtext" rows="3" placeholder="Enter subtext displayed below the Compare Alternatives headline">{{ isset($category_data) ? ($category_data['compare_alternatives_subtext'] ?? '') : old('compare_alternatives_subtext') }}</textarea>
                                </div>
                                <small class="text-muted d-block mt-1">Short text placed immediately below the "Compare {Category} alternatives" Headline.</small>
                                @error('compare_alternatives_subtext')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="community_says_subtext">Community Says Subtext</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control rich-editor" id="community_says_subtext" name="community_says_subtext" rows="3" placeholder="Enter subtext displayed below the What the community says headline">{{ isset($category_data) ? ($category_data['community_says_subtext'] ?? '') : old('community_says_subtext') }}</textarea>
                                </div>
                                <small class="text-muted d-block mt-1">Short text placed immediately below the "What the Localio community says" Headline.</small>
                                @error('community_says_subtext')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="top_rated_description">Top-Rated Page Description</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control rich-editor" id="top_rated_description" name="top_rated_description" rows="4" placeholder="Enter SEO-optimized short description displayed in the 'Explore top-rated categories' section on the Top Rated page">{!! isset($category_data) ? ($category_data['top_rated_description'] ?? '') : old('top_rated_description') !!}</textarea>
                                </div>
                                <small class="text-muted d-block mt-1">Specific description for the Top-Rated page's "Explore top-rated categories" section. If left blank, it falls back to the default description.</small>
                                @error('top_rated_description')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>



                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="meta_title">Meta Title</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="meta_title" name="meta_title"
                                        value="{{ isset($category_data) ? ($category_data['meta_title'] ?? '') : old('meta_title') }}" 
                                        placeholder="Enter SEO meta title for this category/subcategory page" />
                                </div>
                                @error('meta_title')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="meta_description">Meta Description</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control" id="meta_description" name="meta_description" rows="3" 
                                        placeholder="Enter SEO meta description for this category/subcategory page">{{ isset($category_data) ? ($category_data['meta_description'] ?? '') : old('meta_description') }}</textarea>
                                </div>
                                @error('meta_description')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label d-block" for="is_parent">
                                    Category Type
                                </label>
                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="is_parent"
                                        name="is_parent"
                                        value="1"
                                        {{ (!isset($category) || $category->parent_id === null) ? 'checked' : '' }}
                                        {{ $hasSubcategories ? 'disabled' : '' }}
                                        {{ $hasItems ? 'disabled' : '' }}
                                    >
                                    <label class="form-check-label" for="is_parent">
                                        Parent category
                                    </label>
                                </div>
                                @if($hasSubcategories)
                                    <small class="text-warning d-block mt-1">Cannot convert to subcategory: this category contains active subcategories.</small>
                                    <input type="hidden" name="is_parent" value="1" />
                                @endif
                                @if($hasItems)
                                    <small class="text-warning d-block mt-1">Cannot convert to parent category: this category contains active businesses or products.</small>
                                    <input type="hidden" name="is_parent" value="0" />
                                @endif
                                @error('is_parent')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12" id="parent_category_group" style="display: {{ (!isset($category) || $category->parent_id === null) ? 'none' : 'block' }}">
                            <div class="form-group">
                                <label class="form-label" for="parent_id">Select Parent Category</label>
                                <div class="form-control-wrap">
                                    <select class="form-control" name="parent_id" id="parent_id">
                                        <option value="">-- Select Parent --</option>
                                        @foreach($parentCategories as $parent)
                                            <option value="{{ $parent->id }}" 
                                                {{ (isset($category) && $category->parent_id == $parent->id) ? 'selected' : '' }}>
                                                {{ $parent->translation->name ?? 'Unnamed Parent' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('parent_id')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        

                        <!-- FAQ Page Content & Settings Section -->
                        <div class="col-md-12 mt-4">
                            <div class="card card-bordered bg-light">
                                <div class="card-inner py-3">
                                    <h5 class="title mb-1 text-primary"><i class="fa fa-question-circle me-1"></i> FAQ Page Settings & Content</h5>
                                    <p class="text-muted small mb-3">Custom settings and texts displayed exclusively on this category's public FAQs page.</p>

                                    <div class="row g-3">
                                        <!-- FAQ Page H1 Title -->
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label fw-bold" for="faq_h1_title">FAQ Page Title (H1 Heading)</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="faq_h1_title" name="faq_h1_title"
                                                        value="{{ isset($category_data) ? ($category_data['faq_h1_title'] ?? '') : old('faq_h1_title') }}"
                                                        placeholder="e.g. Web Hosting FAQs">
                                                </div>
                                                <small class="text-muted d-block mt-1">Main H1 headline shown at the top of the FAQ page. Defaults to "[Category Name] FAQs" if left empty.</small>
                                                @error('faq_h1_title')
                                                    <div class="error text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- FAQ Page Subtext below H1 with CKEditor -->
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label fw-bold" for="faq_subtext">FAQ Page Subtext (Below H1)</label>
                                                <div class="form-control-wrap">
                                                    <textarea class="form-control rich-editor" id="faq_subtext" name="faq_subtext" rows="3" placeholder="Frequently asked questions and answers about this category on Localio.">{!! isset($category_data) ? ($category_data['faq_subtext'] ?? '') : old('faq_subtext') !!}</textarea>
                                                </div>
                                                <small class="text-muted d-block mt-1">Rich-text description placed directly underneath the FAQ Page H1 title. Defaults to standard intro text if left empty.</small>
                                                @error('faq_subtext')
                                                    <div class="error text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- FAQ Page About Headline (H2) -->
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label fw-bold" for="faq_about_headline">FAQ Page "About" Headline (H2)</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="faq_about_headline" name="faq_about_headline"
                                                        value="{{ isset($category_data) ? ($category_data['faq_about_headline'] ?? '') : old('faq_about_headline') }}"
                                                        placeholder="e.g. About Web hosting">
                                                </div>
                                                <small class="text-muted d-block mt-1">Headline displayed above the introductory text in the FAQ content section. Defaults to "About [Category Name]".</small>
                                                @error('faq_about_headline')
                                                    <div class="error text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12" id="important_category_group">
                            <div class="form-group">
                                <label class="form-label d-block" for="is_important">
                                    Important Category
                                </label>

                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="is_important"
                                        name="is_important"
                                        value="1"
                                        {{ isset($category_data) && $category_data['is_important'] == 1 ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="is_important">
                                        Mark as Important (Header Navigation)
                                    </label>
                                </div>
                            </div>
                        </div>



                        

                        <!-- Rating Criteria Section -->
                        <div class="col-md-12 mt-4" id="rating_criteria_section">
                            <h5 class="title mb-1">Rating Criteria</h5>
                            <p class="text-muted small mb-3">Configure default rating criteria descriptions and define category or subcategory specific criteria.</p>

                            @php
                                $isSubcategory = isset($category) && $category->parent_id !== null;
                            @endphp

                            <!-- Default Rating Criteria (3 Mandatory Defaults) -->
                            @if($isSubcategory)
                                <!-- Read-Only Inherited View for Subcategories -->
                                <div class="card card-bordered bg-light mb-3">
                                    <div class="card-inner py-3">
                                        <h6 class="title mb-2 text-info"><i class="fa fa-lock me-1"></i> Default Rating Criteria (Global Defaults)</h6>
                                        <p class="text-muted small mb-3">These 3 criteria apply to all subcategories and are inherited from the parent main category.</p>
                                        <div class="row g-2">
                                            @foreach($default_criteria as $def)
                                                <div class="col-md-12 p-2 bg-white rounded border mb-2">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="fw-bold me-2">{{ $def['name'] }}</span>
                                                        <span class="badge bg-info">Inherited from Main Category</span>
                                                    </div>
                                                    @if(!empty($def['description']))
                                                        <small class="text-muted d-block">{{ $def['description'] }}</small>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Editable View for Parent/Main Categories -->
                                <div class="card card-bordered bg-light mb-3">
                                    <div class="card-inner py-3">
                                        <h6 class="title mb-2 text-primary"><i class="fa fa-lock me-1"></i> Default Rating Criteria (Global Defaults)</h6>
                                        <p class="text-muted small mb-3">These 3 criteria apply to all categories and cannot be removed. You can add category-specific short descriptions for each.</p>
                                        <div class="row g-2">
                                            @foreach($default_criteria as $def)
                                                <div class="col-md-12 mb-3 p-2 bg-white rounded border">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="fw-bold me-2">{{ $def['name'] }}</span>
                                                        <span class="badge bg-secondary">Default</span>
                                                    </div>
                                                    <input type="text" class="form-control form-control-sm" name="default_rating_criteria[{{ $def['key'] }}][description]" value="{{ $def['description'] ?? '' }}" placeholder="Add short description for {{ $def['name'] }} (shown in review modal)" />
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Inherited Main Category Criteria (if subcategory) -->
                            @if(isset($inherited_criteria) && count($inherited_criteria) > 0)
                                <div class="card card-bordered bg-light mb-3">
                                    <div class="card-inner py-3">
                                        <h6 class="title mb-2 text-info"><i class="fa fa-sitemap me-1"></i> Inherited Main Category Criteria</h6>
                                        <p class="text-muted small mb-3">These criteria are inherited from the parent main category.</p>
                                        <div class="row g-2">
                                            @foreach($inherited_criteria as $inherited)
                                                <div class="col-md-12 p-2 bg-white rounded border mb-2">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="fw-bold me-2">{{ $inherited->name }}</span>
                                                        <span class="badge bg-info">Inherited from Main Category</span>
                                                    </div>
                                                    @if($inherited->description)
                                                        <small class="text-muted d-block">{{ $inherited->description }}</small>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Category / Subcategory Specific Custom Criteria -->
                            <div class="card card-bordered mb-3">
                                <div class="card-inner py-3">
                                    <h6 class="title mb-2"><i class="fa fa-tags me-1"></i> Additional Criteria for this Category / Subcategory</h6>
                                    <p class="text-muted small mb-3">Add custom criteria specific to this category or subcategory.</p>
                                    
                                    <div id="criteria_wrapper">
                                        @if(isset($custom_criteria) && count($custom_criteria) > 0)
                                            @foreach($custom_criteria as $index => $criterion)
                                                <div class="criteria-card border p-3 rounded mb-2 bg-white position-relative">
                                                    <div class="row g-2 align-items-center">
                                                        <div class="col-md-5">
                                                            <label class="form-label mb-1 small fw-bold">Criteria Name</label>
                                                            <input type="text" class="form-control" name="existing_rating_criteria[{{ $criterion->id }}][name]" value="{{ $criterion->name }}" placeholder="e.g. Performance, Scalability" required />
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label mb-1 small fw-bold">Short Description Text</label>
                                                            <input type="text" class="form-control" name="existing_rating_criteria[{{ $criterion->id }}][description]" value="{{ $criterion->description }}" placeholder="Description shown below criteria in review modal" />
                                                        </div>
                                                        <div class="col-md-1 text-end">
                                                            <label class="form-label mb-1 d-block opacity-0">Remove</label>
                                                            <button type="button" class="btn btn-outline-danger remove-criteria"><i class="fa fa-trash"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add_criteria_btn">
                                        <i class="fa fa-plus me-1"></i> Add Additional Criteria
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Rich Text Sections (H2 & H3 Sub-headlines) -->
                        <div class="col-md-12 mt-4" id="text_sections_wrapper">
                            <div class="card card-bordered">
                                <div class="card-inner py-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <h5 class="title mb-0 text-primary"><i class="fa fa-file-text-o me-1"></i> Dynamic Content Sections (H2 & H3 Sub-headlines)</h5>
                                            <p class="text-muted small mb-0">Add custom content sections with an H2 headline, rich text editor, and optional nested H3 sub-headlines.</p>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-primary" id="addCategoryTextSectionBtn">
                                            <i class="fa fa-plus me-1"></i> + Add Section (H2)
                                        </button>
                                    </div>
                                    <hr class="my-3">
                                    <div id="categoryTextSectionsContainer">
                                        @if(isset($text_sections) && is_array($text_sections))
                                            @foreach($text_sections as $sIndex => $sec)
                                            <div class="section-card border rounded p-3 mb-3 bg-white" data-section-index="{{ $sIndex }}">
                                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                                    <h6 class="fw-bold mb-0 text-dark">Section {{ $sIndex + 1 }}</h6>
                                                    <button type="button" class="btn btn-sm btn-danger remove-section-btn">Delete Section</button>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="form-label fw-bold">Headline (H2)</label>
                                                        <input type="text" class="form-control" name="text_sections[{{ $sIndex }}][h2_title]" value="{{ $sec['h2_title'] ?? '' }}" placeholder="e.g. About web hosting">
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label fw-bold">Headline Content (Rich Editor)</label>
                                                        <textarea class="form-control rich-editor" rows="4" name="text_sections[{{ $sIndex }}][h2_text]" placeholder="Enter main content for this H2 section...">{!! $sec['h2_text'] ?? '' !!}</textarea>
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
                                                                    <input type="text" class="form-control form-control-sm mb-2" name="text_sections[{{ $sIndex }}][sub_sections][{{ $subIndex }}][h3_title]" value="{{ $sub['h3_title'] ?? '' }}" placeholder="e.g. What is web hosting?">
                                                                    <textarea class="form-control rich-editor" rows="3" name="text_sections[{{ $sIndex }}][sub_sections][{{ $subIndex }}][h3_text]" placeholder="Enter sub-headline content...">{!! $sub['h3_text'] ?? '' !!}</textarea>
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
                        </div>

                        

                        <!-- Submit Button -->
                        <div class="col-md-12 mt-4">
                            <div class="form-group">
                                <button type="submit" class="btn btn-lg btn-primary btn-localio">{{isset($category_data)?'Update Category':'Save Category' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isParentCheckbox = document.getElementById('is_parent');
            const parentCategoryGroup = document.getElementById('parent_category_group');
            const parentCategorySelect = document.getElementById('parent_id');

            function toggleParentDropdown() {
                if (isParentCheckbox && isParentCheckbox.checked) {
                    parentCategoryGroup.style.display = 'none';
                    if (parentCategorySelect) {
                        parentCategorySelect.value = '';
                    }
                } else {
                    parentCategoryGroup.style.display = 'block';
                }
            }

            if (isParentCheckbox) {
                isParentCheckbox.addEventListener('change', toggleParentDropdown);
            }

            // Comparison Slug Auto-generate & Pencil Edit Toggle
            const nameInput = document.getElementById('name');
            const comparisonSlugInput = document.getElementById('comparison_slug');
            const editComparisonSlugBtn = document.getElementById('edit_comparison_slug_btn');
            let isSlugManuallyEdited = {{ (isset($category_data) && !empty($category_data['comparison_slug'])) || old('comparison_slug') ? 'true' : 'false' }};

            function generateSlug(text) {
                return text
                    .toString()
                    .toLowerCase()
                    .trim()
                    .replace(/&/g, '-and-')
                    .replace(/[\s\W-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }

            function updateComparisonSlug() {
                if (!isSlugManuallyEdited && nameInput && comparisonSlugInput) {
                    const rawSlug = generateSlug(nameInput.value);
                    comparisonSlugInput.value = rawSlug ? rawSlug + '-comparison' : '';
                }
            }

            if (nameInput && comparisonSlugInput) {
                if (!comparisonSlugInput.value && nameInput.value.trim()) {
                    updateComparisonSlug();
                }

                nameInput.addEventListener('input', function() {
                    updateComparisonSlug();
                });

                comparisonSlugInput.addEventListener('input', function() {
                    isSlugManuallyEdited = true;
                });

                comparisonSlugInput.addEventListener('blur', function() {
                    if (this.value.trim()) {
                        this.value = generateSlug(this.value);
                    }
                });
            }

            if (editComparisonSlugBtn && comparisonSlugInput) {
                editComparisonSlugBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (comparisonSlugInput.readOnly) {
                        comparisonSlugInput.readOnly = false;
                        comparisonSlugInput.focus();
                        isSlugManuallyEdited = true;
                        editComparisonSlugBtn.innerHTML = '<em class="icon ni ni-check"></em>';
                        editComparisonSlugBtn.classList.remove('btn-outline-light', 'btn-white');
                        editComparisonSlugBtn.classList.add('btn-primary');
                        editComparisonSlugBtn.title = 'Lock Slug';
                    } else {
                        comparisonSlugInput.readOnly = true;
                        editComparisonSlugBtn.innerHTML = '<em class="icon ni ni-edit"></em>';
                        editComparisonSlugBtn.classList.add('btn-outline-light', 'btn-white');
                        editComparisonSlugBtn.classList.remove('btn-primary');
                        editComparisonSlugBtn.title = 'Edit Comparison Slug';
                    }
                });
            }

            // Rating Criteria Logic
            const addCriteriaBtn = document.getElementById('add_criteria_btn');
            const criteriaWrapper = document.getElementById('criteria_wrapper');
            let newCriteriaIndex = 0;

            if (addCriteriaBtn && criteriaWrapper) {
                addCriteriaBtn.addEventListener('click', function() {
                    newCriteriaIndex++;
                    const card = document.createElement('div');
                    card.className = 'criteria-card border p-3 rounded mb-2 bg-white position-relative';
                    card.innerHTML = `
                        <div class="row g-2 align-items-center">
                            <div class="col-md-5">
                                <label class="form-label mb-1 small fw-bold">Criteria Name</label>
                                <input type="text" class="form-control" name="new_rating_criteria[${newCriteriaIndex}][name]" placeholder="e.g. Server management" required />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-1 small fw-bold">Short Description Text</label>
                                <input type="text" class="form-control" name="new_rating_criteria[${newCriteriaIndex}][description]" placeholder="Description shown below criteria in review modal" />
                            </div>
                            <div class="col-md-1 text-end">
                                <label class="form-label mb-1 d-block opacity-0">Remove</label>
                                <button type="button" class="btn btn-outline-danger remove-criteria"><i class="fa fa-trash"></i></button>
                            </div>
                        </div>
                    `;
                    criteriaWrapper.appendChild(card);
                });

                criteriaWrapper.addEventListener('click', function(e) {
                    const removeBtn = e.target.closest('.remove-criteria');
                    if (removeBtn) {
                        removeBtn.closest('.criteria-card').remove();
                    }
                });
            }
        });
    
        // CKEditor helper functions
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

        // Initialize on load
        initAllCKEditors();

        let catSectionCounter = {{ isset($text_sections) && is_array($text_sections) ? count($text_sections) : 0 }};

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
                <input type="text" class="form-control form-control-sm mb-2" name="text_sections[${sIdx}][sub_sections][${subIdx}][h3_title]" placeholder="e.g. What is web hosting?">
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

        $('#addCategoryFaqBtn').on('click', function() {
            let fIdx = catFaqCounter++;
            let html = `
            <div class="faq-item-card border rounded p-3 mb-3 bg-white" data-faq-index="${fIdx}">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">FAQ #${fIdx + 1}</h6>
                    <button type="button" class="btn btn-sm btn-danger remove-faq-btn">Delete</button>
                </div>
                <div class="row g-2">
                    <div class="col-12">
                        <label class="form-label fw-bold">Question</label>
                        <input type="text" class="form-control" name="category_faqs[${fIdx}][question]" placeholder="Enter FAQ question...">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Answer (Rich Editor)</label>
                        <textarea class="form-control rich-editor" rows="3" name="category_faqs[${fIdx}][answer]" placeholder="Enter FAQ answer..."></textarea>
                    </div>
                </div>
            </div>`;
            $('#categoryFaqsContainer').append(html);
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
        $('form.form-validate').on('submit', function() {
            document.querySelectorAll('textarea.rich-editor').forEach(el => {
                if (el.ckeditorInstance) {
                    el.value = el.ckeditorInstance.getData();
                }
            });
        });
</script>
@endsection