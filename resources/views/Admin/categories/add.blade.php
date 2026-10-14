@extends('admin_layout.master')
@section('content')
    <style>
        .modal-backdrop {
            z-index: 1040 !important;
        }
        .modal {
            z-index: 1050 !important;
        }
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
        <div class="nk-block-head d-flex justify-content-between align-items-center mb-3">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">
                    {{ isset($category_data) ? 'Update Category' : 'Add Category' }}
                </h4>
                <div class="nk-block-des text-soft">
                    <p>{{ isset($category_data) ? 'Edit category or subcategory details, SEO placeholders, and dynamic content.' : 'Create a new parent category or subcategory with placeholders, rating criteria, and content.' }}</p>
                </div>
            </div>
            <div class="nk-block-head-content">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-primary" id="openJsonModalBtn" data-toggle="modal" data-target="#categoryJsonModal">
                        <em class="icon ni ni-code me-1"></em> Fill with JSON
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="exportCategoryJsonBtn" title="Export current form data as JSON">
                        <em class="icon ni ni-download me-1"></em> Export JSON
                    </button>
                </div>
            </div>
        </div>
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('add-category-process') }}" class="form-validate" novalidate="novalidate" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="category_id" value="{{isset($category_data)?$category_data['id']:''}}" />
                    <div class="row g-gs">
                        {{-- 1. Category Name Placeholders --}}
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary mb-1"><i class="fa fa-tag me-1"></i> Category Name Placeholders</h6>
                            <small class="text-muted d-block mb-3">Placeholders used in SEO templates and dynamic content generation.</small>
                        </div>

                        {{-- Name capitalized [category_cap] --}}
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="category_cap">Name capitalized <code class="text-primary">[category_cap]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="category_cap" name="category_cap"
                                        value="{{ isset($category_data) ? ($category_data['category_cap'] ?? ($category_data['name'] ?? '')) : old('category_cap', old('name')) }}" />
                                    <input type="hidden" id="name" name="name"
                                        value="{{ isset($category_data) ? ($category_data['name'] ?? ($category_data['category_cap'] ?? '')) : old('name') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: Web hosting</small>
                                @error('category_cap')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                                @error('name')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Name title case [category_tc] --}}
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="category_tc">Name title case <code class="text-primary">[category_tc]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="category_tc" name="category_tc"
                                        value="{{ isset($category_data) ? ($category_data['category_tc'] ?? '') : old('category_tc') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: Web Hosting</small>
                                @error('category_tc')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Name lowercase [category_lc] --}}
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="category_lc">Name lowercase <code class="text-primary">[category_lc]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="category_lc" name="category_lc"
                                        value="{{ isset($category_data) ? ($category_data['category_lc'] ?? '') : old('category_lc') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: web hosting</small>
                                @error('category_lc')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- 2. Entity 1 Placeholders --}}
                        <div class="col-12 mt-4 pt-3 border-top">
                            <h6 class="fw-bold text-primary mb-1"><i class="fa fa-building-o me-1"></i> Entity 1 Placeholders (e.g. Provider)</h6>
                            <small class="text-muted d-block mb-3">Primary entity descriptors for this category (e.g. provider, business, platform).</small>
                        </div>

                        {{-- Entity 1 singular capitalized [entity1_cap] --}}
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="entity1_cap">Entity 1 singular capitalized <code class="text-primary">[entity1_cap]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="entity1_cap" name="entity1_cap"
                                        value="{{ isset($category_data) ? ($category_data['entity1_cap'] ?? '') : old('entity1_cap') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: Web hosting provider</small>
                                @error('entity1_cap')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Entity 1 singular lowercase [entity1_lc] --}}
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="entity1_lc">Entity 1 singular lowercase <code class="text-primary">[entity1_lc]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="entity1_lc" name="entity1_lc"
                                        value="{{ isset($category_data) ? ($category_data['entity1_lc'] ?? '') : old('entity1_lc') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: web hosting provider</small>
                                @error('entity1_lc')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Entity 1 plural capitalized [entities1_cap] --}}
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="entities1_cap">Entity 1 plural capitalized <code class="text-primary">[entities1_cap]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="entities1_cap" name="entities1_cap"
                                        value="{{ isset($category_data) ? ($category_data['entities1_cap'] ?? '') : old('entities1_cap') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: Web hosting providers</small>
                                @error('entities1_cap')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Entity 1 plural lowercase [entities1_lc] --}}
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="entities1_lc">Entity 1 plural lowercase <code class="text-primary">[entities1_lc]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="entities1_lc" name="entities1_lc"
                                        value="{{ isset($category_data) ? ($category_data['entities1_lc'] ?? '') : old('entities1_lc') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: web hosting providers</small>
                                @error('entities1_lc')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Entity 1 short singular capitalized [entity1_short_cap] --}}
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="entity1_short_cap">Entity 1 short singular capitalized <code class="text-primary">[entity1_short_cap]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="entity1_short_cap" name="entity1_short_cap"
                                        value="{{ isset($category_data) ? ($category_data['entity1_short_cap'] ?? '') : old('entity1_short_cap') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: Provider</small>
                                @error('entity1_short_cap')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Entity 1 short singular lowercase [entity1_short_lc] --}}
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="entity1_short_lc">Entity 1 short singular lowercase <code class="text-primary">[entity1_short_lc]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="entity1_short_lc" name="entity1_short_lc"
                                        value="{{ isset($category_data) ? ($category_data['entity1_short_lc'] ?? '') : old('entity1_short_lc') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: provider</small>
                                @error('entity1_short_lc')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Entity 1 short plural capitalized [entities1_short_cap] --}}
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="entities1_short_cap">Entity 1 short plural capitalized <code class="text-primary">[entities1_short_cap]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="entities1_short_cap" name="entities1_short_cap"
                                        value="{{ isset($category_data) ? ($category_data['entities1_short_cap'] ?? '') : old('entities1_short_cap') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: Providers</small>
                                @error('entities1_short_cap')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Entity 1 short plural lowercase [entities1_short_lc] --}}
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="entities1_short_lc">Entity 1 short plural lowercase <code class="text-primary">[entities1_short_lc]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="entities1_short_lc" name="entities1_short_lc"
                                        value="{{ isset($category_data) ? ($category_data['entities1_short_lc'] ?? '') : old('entities1_short_lc') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: providers</small>
                                @error('entities1_short_lc')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- 3. Entity 2 Placeholders --}}
                        <div class="col-12 mt-4 pt-3 border-top">
                            <h6 class="fw-bold text-primary mb-1"><i class="fa fa-cogs me-1"></i> Entity 2 Placeholders (e.g. Service)</h6>
                            <small class="text-muted d-block mb-3">Secondary entity descriptors for this category (e.g. service, tool, solution).</small>
                        </div>

                        {{-- Entity 2 singular capitalized [entity2_cap] --}}
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="entity2_cap">Entity 2 singular capitalized <code class="text-primary">[entity2_cap]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="entity2_cap" name="entity2_cap"
                                        value="{{ isset($category_data) ? ($category_data['entity2_cap'] ?? '') : old('entity2_cap') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: Web hosting service</small>
                                @error('entity2_cap')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Entity 2 singular lowercase [entity2_lc] --}}
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="entity2_lc">Entity 2 singular lowercase <code class="text-primary">[entity2_lc]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="entity2_lc" name="entity2_lc"
                                        value="{{ isset($category_data) ? ($category_data['entity2_lc'] ?? '') : old('entity2_lc') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: web hosting service</small>
                                @error('entity2_lc')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Entity 2 plural capitalized [entities2_cap] --}}
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="entities2_cap">Entity 2 plural capitalized <code class="text-primary">[entities2_cap]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="entities2_cap" name="entities2_cap"
                                        value="{{ isset($category_data) ? ($category_data['entities2_cap'] ?? '') : old('entities2_cap') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: Web hosting services</small>
                                @error('entities2_cap')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Entity 2 plural lowercase [entities2_lc] --}}
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="entities2_lc">Entity 2 plural lowercase <code class="text-primary">[entities2_lc]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="entities2_lc" name="entities2_lc"
                                        value="{{ isset($category_data) ? ($category_data['entities2_lc'] ?? '') : old('entities2_lc') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: web hosting services</small>
                                @error('entities2_lc')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Entity 2 short singular capitalized [entity2_short_cap] --}}
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="entity2_short_cap">Entity 2 short singular capitalized <code class="text-primary">[entity2_short_cap]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="entity2_short_cap" name="entity2_short_cap"
                                        value="{{ isset($category_data) ? ($category_data['entity2_short_cap'] ?? '') : old('entity2_short_cap') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: Service</small>
                                @error('entity2_short_cap')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Entity 2 short singular lowercase [entity2_short_lc] --}}
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="entity2_short_lc">Entity 2 short singular lowercase <code class="text-primary">[entity2_short_lc]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="entity2_short_lc" name="entity2_short_lc"
                                        value="{{ isset($category_data) ? ($category_data['entity2_short_lc'] ?? '') : old('entity2_short_lc') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: service</small>
                                @error('entity2_short_lc')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Entity 2 short plural capitalized [entities2_short_cap] --}}
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="entities2_short_cap">Entity 2 short plural capitalized <code class="text-primary">[entities2_short_cap]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="entities2_short_cap" name="entities2_short_cap"
                                        value="{{ isset($category_data) ? ($category_data['entities2_short_cap'] ?? '') : old('entities2_short_cap') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: Services</small>
                                @error('entities2_short_cap')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Entity 2 short plural lowercase [entities2_short_lc] --}}
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="entities2_short_lc">Entity 2 short plural lowercase <code class="text-primary">[entities2_short_lc]</code></label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="entities2_short_lc" name="entities2_short_lc"
                                        value="{{ isset($category_data) ? ($category_data['entities2_short_lc'] ?? '') : old('entities2_short_lc') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: services</small>
                                @error('entities2_short_lc')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 mt-4 pt-3 border-top"></div>
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
                                        value="{{ old('page_title_addition', isset($category) ? ($category->page_title_addition ?? ($category_data['category']['page_title_addition'] ?? '')) : '[country2] ([year])') }}" 
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

                        {{-- Basic Business Information ("At a glance") Labels for Options 3 & 4 --}}
                        <div class="col-12 mt-4 pt-3 border-top">
                            <h6 class="fw-bold text-primary mb-1"><i class="fa fa-list-alt me-1"></i> Basic Business Information ("At a glance") Labels</h6>
                            <small class="text-muted d-block mb-3">Define category-specific attribute labels for options 3 &amp; 4 displayed in the business profile "At a glance" card. If left blank on a subcategory, it inherits from the parent category.</small>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="glance_option_3_label">Option 3 Label</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="glance_option_3_label" name="glance_option_3_label"
                                        placeholder="e.g. Hosting options"
                                        value="{{ isset($category_data) ? ($category_data['glance_option_3_label'] ?? '') : old('glance_option_3_label') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: Hosting options (leave blank on subcategory to inherit from parent)</small>
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="glance_option_4_label">Option 4 Label</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="glance_option_4_label" name="glance_option_4_label"
                                        placeholder="e.g. Website tools"
                                        value="{{ isset($category_data) ? ($category_data['glance_option_4_label'] ?? '') : old('glance_option_4_label') }}" />
                                </div>
                                <small class="text-muted d-block mt-1">Example: Website tools (leave blank on subcategory to inherit from parent)</small>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="description">Header Text (First Introductory Paragraph)</label>
                                <small class="text-muted d-block mb-1">Enter the individual introductory overview sentence(s) for this category (e.g. up to "...need to handle directly."). The closing question sentence and the second overview paragraph are automatically appended from the assigned Content Template.</small>
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

                        <!-- 1. Category / Subcategory Page Overlay SVG (150x150) -->
                        <div class="col-md-12">
                            <div class="card card-bordered p-3 mb-3 bg-light" style="border-radius: 8px;">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-7 col-12">
                                        <div class="form-group">
                                            <label class="form-label fw-bold" for="category_overlay_image">Category Page Overlay SVG (150x150)</label>
                                            <div class="form-control-wrap">
                                                <div class="form-file">
                                                    <input type="file" class="form-file-input" id="category_overlay_image" name="category_overlay_image" accept="image/svg+xml,image/*">
                                                    <label class="form-file-label" for="category_overlay_image">Choose Overlay SVG</label>
                                                </div>
                                            </div>
                                            <small class="text-muted d-block mt-1">Small overlay graphic placed over the bottom area of the category hero image.</small>
                                            @if(isset($category) && $category->category_overlay_image && $category->categoryOverlayMedia)
                                                <div class="d-flex align-items-center gap-3 mt-2">
                                                    <img src="{{ asset($category->categoryOverlayMedia->dir_path . '/' . $category->categoryOverlayMedia->file_name) }}" alt="Category Overlay" style="max-width: 50px; max-height: 50px; object-fit: contain; background: #fff; padding: 4px; border: 1px solid #e2e8f0; border-radius: 6px;">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="remove_category_overlay_image" value="1" id="remove_cat_overlay">
                                                        <label class="form-check-label text-danger small" for="remove_cat_overlay">Remove current overlay</label>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-5 col-12">
                                        <div class="form-group">
                                            <label class="form-label fw-bold" for="category_overlay_position">Position (Bottom Area)</label>
                                            <div class="form-control-wrap">
                                                <select class="form-select" id="category_overlay_position" name="category_overlay_position">
                                                    <option value="left" {{ (isset($category) && $category->category_overlay_position == 'left') ? 'selected' : '' }}>Left</option>
                                                    <option value="center" {{ (isset($category) && $category->category_overlay_position == 'center') ? 'selected' : '' }}>Center</option>
                                                    <option value="right" {{ (!isset($category) || !$category->category_overlay_position || $category->category_overlay_position == 'right') ? 'selected' : '' }}>Right</option>
                                                </select>
                                            </div>
                                            <small class="text-muted d-block mt-1">Horizontal placement: Left, Center, or Right.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. FAQ Page Overlay SVG (150x150) -->
                        <div class="col-md-12">
                            <div class="card card-bordered p-3 mb-3 bg-light" style="border-radius: 8px;">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-7 col-12">
                                        <div class="form-group">
                                            <label class="form-label fw-bold" for="faq_overlay_image">FAQ Page Overlay SVG (150x150)</label>
                                            <div class="form-control-wrap">
                                                <div class="form-file">
                                                    <input type="file" class="form-file-input" id="faq_overlay_image" name="faq_overlay_image" accept="image/svg+xml,image/*">
                                                    <label class="form-file-label" for="faq_overlay_image">Choose Overlay SVG</label>
                                                </div>
                                            </div>
                                            <small class="text-muted d-block mt-1">Small overlay graphic placed over the bottom area of the FAQ hero image.</small>
                                            @if(isset($category) && $category->faq_overlay_image && $category->faqOverlayMedia)
                                                <div class="d-flex align-items-center gap-3 mt-2">
                                                    <img src="{{ asset($category->faqOverlayMedia->dir_path . '/' . $category->faqOverlayMedia->file_name) }}" alt="FAQ Overlay" style="max-width: 50px; max-height: 50px; object-fit: contain; background: #fff; padding: 4px; border: 1px solid #e2e8f0; border-radius: 6px;">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="remove_faq_overlay_image" value="1" id="remove_faq_overlay">
                                                        <label class="form-check-label text-danger small" for="remove_faq_overlay">Remove current overlay</label>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-5 col-12">
                                        <div class="form-group">
                                            <label class="form-label fw-bold" for="faq_overlay_position">Position (Bottom Area)</label>
                                            <div class="form-control-wrap">
                                                <select class="form-select" id="faq_overlay_position" name="faq_overlay_position">
                                                    <option value="left" {{ (isset($category) && $category->faq_overlay_position == 'left') ? 'selected' : '' }}>Left</option>
                                                    <option value="center" {{ (isset($category) && $category->faq_overlay_position == 'center') ? 'selected' : '' }}>Center</option>
                                                    <option value="right" {{ (!isset($category) || !$category->faq_overlay_position || $category->faq_overlay_position == 'right') ? 'selected' : '' }}>Right</option>
                                                </select>
                                            </div>
                                            <small class="text-muted d-block mt-1">Horizontal placement: Left, Center, or Right.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Comparison Page Overlay SVG (150x150) -->
                        <div class="col-md-12">
                            <div class="card card-bordered p-3 mb-3 bg-light" style="border-radius: 8px;">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-7 col-12">
                                        <div class="form-group">
                                            <label class="form-label fw-bold" for="comparison_overlay_image">Comparison Page Overlay SVG (150x150)</label>
                                            <div class="form-control-wrap">
                                                <div class="form-file">
                                                    <input type="file" class="form-file-input" id="comparison_overlay_image" name="comparison_overlay_image" accept="image/svg+xml,image/*">
                                                    <label class="form-file-label" for="comparison_overlay_image">Choose Overlay SVG</label>
                                                </div>
                                            </div>
                                            <small class="text-muted d-block mt-1">Small overlay graphic placed over the bottom area of the Comparison hero image.</small>
                                            @if(isset($category) && $category->comparison_overlay_image && $category->comparisonOverlayMedia)
                                                <div class="d-flex align-items-center gap-3 mt-2">
                                                    <img src="{{ asset($category->comparisonOverlayMedia->dir_path . '/' . $category->comparisonOverlayMedia->file_name) }}" alt="Comparison Overlay" style="max-width: 50px; max-height: 50px; object-fit: contain; background: #fff; padding: 4px; border: 1px solid #e2e8f0; border-radius: 6px;">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="remove_comparison_overlay_image" value="1" id="remove_comp_overlay">
                                                        <label class="form-check-label text-danger small" for="remove_comp_overlay">Remove current overlay</label>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-5 col-12">
                                        <div class="form-group">
                                            <label class="form-label fw-bold" for="comparison_overlay_position">Position (Bottom Area)</label>
                                            <div class="form-control-wrap">
                                                <select class="form-select" id="comparison_overlay_position" name="comparison_overlay_position">
                                                    <option value="left" {{ (isset($category) && $category->comparison_overlay_position == 'left') ? 'selected' : '' }}>Left</option>
                                                    <option value="center" {{ (isset($category) && $category->comparison_overlay_position == 'center') ? 'selected' : '' }}>Center</option>
                                                    <option value="right" {{ (!isset($category) || !$category->comparison_overlay_position || $category->comparison_overlay_position == 'right') ? 'selected' : '' }}>Right</option>
                                                </select>
                                            </div>
                                            <small class="text-muted d-block mt-1">Horizontal placement: Left, Center, or Right.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="alert alert-primary bg-primary-dim border-0 d-flex align-items-center justify-content-between p-3 mb-3">
                                <div>
                                    <strong class="d-block text-primary"><em class="icon ni ni-template me-1"></em>Dynamic Content Templates</strong>
                                    <span class="small text-muted">Headlines and subtexts for Top 10, Compare Top-Rated, Pros &amp; Cons, Comparisons, Category FAQs, FAQ Search, and Discussions are centrally managed in <strong>Content templates &gt; Category</strong>.</span>
                                </div>
                                <a href="{{ route('admin.content-templates.categories') }}" target="_blank" class="btn btn-sm btn-primary ms-3 text-nowrap">
                                    <em class="icon ni ni-external me-1"></em> Manage Content Templates
                                </a>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="alert alert-primary bg-primary-dim border-0 d-flex align-items-center justify-content-between p-3">
                                <div>
                                    <strong class="d-block text-primary"><em class="icon ni ni-tags me-1"></em>SEO Meta Tags (Title & Description)</strong>
                                    <span class="small text-muted">Category meta tags and automated character-limit fallbacks are now centrally managed in the <strong>Meta Tags &gt; Categories</strong> section.</span>
                                </div>
                                <a href="{{ route('admin.meta-tags.categories') }}" target="_blank" class="btn btn-sm btn-primary ms-3 text-nowrap">
                                    <em class="icon ni ni-external me-1"></em> Manage Meta Tags
                                </a>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="alert alert-info bg-info-dim border-0 d-flex align-items-center justify-content-between p-3">
                                <div>
                                    <strong class="d-block text-info"><em class="icon ni ni-help me-1"></em>SEO Meta Tags (Category FAQs Page)</strong>
                                    <span class="small text-muted">Category FAQ meta titles, descriptions, and dynamic 3-tier cascade fallbacks are now centrally managed in <strong>Meta Tags &gt; Categories FAQs</strong>.</span>
                                </div>
                                <a href="{{ route('admin.meta-tags.categories-faqs') }}" target="_blank" class="btn btn-sm btn-info ms-3 text-nowrap">
                                    <em class="icon ni ni-external me-1"></em> Manage FAQ Meta Tags
                                </a>
                            </div>
                        </div>
                        {{-- Category Type Selection --}}
                        <div class="col-md-12">
                            <div class="card card-bordered p-3 mb-2 bg-light" style="border-radius: 8px;">
                                <div class="form-group mb-0">
                                    <label class="form-label fw-bold d-block mb-2">
                                        <i class="fa fa-sitemap me-1 text-primary"></i> Category Type
                                    </label>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio custom-control-pro no-control p-3 bg-white border rounded h-100" id="card_type_parent">
                                                <input type="radio" class="custom-control-input category-type-radio" name="is_parent" id="type_parent" value="1"
                                                    {{ (!isset($category) || $category->parent_id === null) ? 'checked' : '' }}
                                                    {{ $hasItems ? 'disabled' : '' }}>
                                                <label class="custom-control-label w-100 d-block cursor-pointer" for="type_parent">
                                                    <span class="d-flex align-items-center mb-1">
                                                        <em class="icon ni ni-folder text-primary me-2" style="font-size: 1.25rem;"></em>
                                                        <strong class="text-dark">Main / Parent Category</strong>
                                                    </span>
                                                    <small class="text-muted d-block ps-4">Top-level main category that can contain subcategories.</small>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio custom-control-pro no-control p-3 bg-white border rounded h-100" id="card_type_subcategory">
                                                <input type="radio" class="custom-control-input category-type-radio" name="is_parent" id="type_subcategory" value="0"
                                                    {{ (isset($category) && $category->parent_id !== null) ? 'checked' : '' }}
                                                    {{ $hasSubcategories ? 'disabled' : '' }}>
                                                <label class="custom-control-label w-100 d-block cursor-pointer" for="type_subcategory">
                                                    <span class="d-flex align-items-center mb-1">
                                                        <em class="icon ni ni-folder-sub text-info me-2" style="font-size: 1.25rem;"></em>
                                                        <strong class="text-dark">Subcategory (Child Category)</strong>
                                                    </span>
                                                    <small class="text-muted d-block ps-4">Assigns this category as a child under a parent main category.</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($hasSubcategories)
                                        <div class="alert alert-warning py-2 px-3 small mt-2 mb-0">
                                            <em class="icon ni ni-alert-circle me-1"></em> Cannot convert to subcategory: this category already contains active subcategories.
                                        </div>
                                        <input type="hidden" name="is_parent" value="1" />
                                    @endif
                                    @if($hasItems)
                                        <div class="alert alert-warning py-2 px-3 small mt-2 mb-0">
                                            <em class="icon ni ni-alert-circle me-1"></em> Cannot convert to parent category: this subcategory contains active businesses or products.
                                        </div>
                                        <input type="hidden" name="is_parent" value="0" />
                                    @endif
                                    @error('is_parent')
                                        <div class="error text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Parent Category Dropdown Group --}}
                        <div class="col-md-12" id="parent_category_group" style="display: {{ (!isset($category) || $category->parent_id === null) ? 'none' : 'block' }};">
                            <div class="card card-bordered p-3 mb-3 bg-white border-primary" style="border-radius: 8px;">
                                <div class="form-group mb-0">
                                    <label class="form-label fw-bold" for="parent_id">
                                        <span class="text-danger">*</span> Select Parent Category
                                    </label>
                                    <div class="form-control-wrap">
                                        <select class="form-control form-select" name="parent_id" id="parent_id">
                                            <option value="">-- Select Parent Category --</option>
                                            @foreach($parentCategories as $parent)
                                                @php
                                                    $parentName = $parent->translation->name ?? ($parent->categoryTranslations->first()->name ?? 'Category #' . $parent->id);
                                                @endphp
                                                <option value="{{ $parent->id }}" 
                                                    {{ (isset($category) && $category->parent_id == $parent->id) ? 'selected' : '' }}>
                                                    {{ $parentName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <small class="text-muted d-block mt-1">This subcategory will be grouped and nested under the selected parent category.</small>
                                    @error('parent_id')
                                        <div class="error text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
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
                                            <div class="alert alert-light border bg-white d-flex align-items-center justify-content-between p-3 mb-2">
                                                <div>
                                                    <strong class="d-block text-dark"><em class="icon ni ni-template me-1"></em>FAQ Content Templates</strong>
                                                    <span class="small text-muted">FAQ Second Headline, About Description, and Explore Categories texts are managed in <strong>Content templates &gt; Category FAQs</strong>.</span>
                                                </div>
                                                <a href="{{ route('admin.content-templates.categories-faqs') }}" target="_blank" class="btn btn-sm btn-outline-info text-nowrap">
                                                    <em class="icon ni ni-external me-1"></em> Manage FAQ Templates
                                                </a>
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
        {{-- Fill Category with JSON Modal --}}
    <div class="modal fade" id="categoryJsonModal" tabindex="-1" role="dialog" aria-labelledby="categoryJsonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <div>
                        <h5 class="modal-title fw-bold text-primary" id="categoryJsonModalLabel">
                            <em class="icon ni ni-code me-1"></em> Fill Category / Subcategory with JSON
                        </h5>
                        <p class="text-muted small mb-0">Paste structured JSON to instantly fill placeholders, category type, parent assignment, rating criteria, and content sections.</p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                        <label class="form-label fw-bold mb-0">JSON Payload</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-xs btn-outline-primary" id="loadSampleCategoryJsonBtn">
                                <em class="icon ni ni-folder me-1"></em>Sample Category
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-info" id="loadSampleSubcategoryJsonBtn">
                                <em class="icon ni ni-folder-sub me-1"></em>Sample Subcategory
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-secondary" id="copySampleJsonBtn">
                                <em class="icon ni ni-copy me-1"></em>Copy
                            </button>
                            <button type="button" class="btn btn-xs btn-light text-muted" id="clearJsonModalBtn">
                                Clear
                            </button>
                        </div>
                    </div>

                    <div class="form-control-wrap">
                        <textarea id="categoryJsonInput" class="form-control font-monospace text-dark" rows="12" style="font-size: 0.85rem; line-height: 1.45;" placeholder="Paste JSON here..."></textarea>
                    </div>

                    <div id="categoryJsonError" class="alert alert-danger py-2 px-3 small mt-2" style="display: none;"></div>
                    <div id="categoryJsonSuccess" class="alert alert-success py-2 px-3 small mt-2" style="display: none;"></div>

                    <div class="border rounded p-2 mt-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center cursor-pointer" data-toggle="collapse" data-target="#jsonFormatInfo" data-bs-toggle="collapse" data-bs-target="#jsonFormatInfo">
                            <span class="small fw-bold text-primary"><em class="icon ni ni-help-fill me-1"></em> Supported JSON fields and structure</span>
                            <em class="icon ni ni-chevron-down text-muted"></em>
                        </div>
                        <div class="collapse mt-2 pt-2 border-top small text-muted" id="jsonFormatInfo">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <strong class="d-block text-dark">Category Type & Parent:</strong>
                                    <code>category_type</code>: <code>"parent"</code> or <code>"subcategory"</code><br>
                                    <code>parent_name</code>: e.g. <code>"Web Hosting"</code> (auto-selects from parent dropdown)<br>
                                    <code>parent_id</code>: e.g. <code>5</code><br>
                                    <code>is_important</code>: <code>true</code> or <code>false</code>
                                </div>
                                <div class="col-md-6">
                                    <strong class="d-block text-dark">Names & Titles:</strong>
                                    <code>category_cap</code>: e.g. <code>"Web hosting"</code><br>
                                    <code>category_tc</code>: e.g. <code>"Web Hosting"</code><br>
                                    <code>category_lc</code>: e.g. <code>"web hosting"</code><br>
                                    <code>page_title</code>, <code>page_title_addition</code>, <code>comparison_slug</code>
                                </div>
                                <div class="col-md-6">
                                    <strong class="d-block text-dark">Entities:</strong>
                                    <code>entity1_cap</code>, <code>entity1_lc</code>, <code>entities1_cap</code>, <code>entities1_lc</code>, <code>entity1_short_cap</code>, <code>entity1_short_lc</code>...<br>
                                    <code>entity2_cap</code>, <code>entity2_lc</code>, <code>entities2_cap</code>, <code>entities2_lc</code>, <code>entity2_short_cap</code>, <code>entity2_short_lc</code>...
                                </div>
                                <div class="col-md-6">
                                    <strong class="d-block text-dark">Content & Criteria:</strong>
                                    <code>glance_option_3_label</code>, <code>glance_option_4_label</code><br>
                                    <code>description</code>: Overview / header paragraph (HTML)<br>
                                    <code>default_rating_criteria</code>: <code>{"pricing_value": "...", "features_functionality": "..."}</code><br>
                                    <code>custom_rating_criteria</code>: <code>[{"name": "...", "description": "..."}]</code><br>
                                    <code>text_sections</code>: <code>[{"h2_title": "...", "h2_text": "...", "sub_sections": [...]}]</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="applyCategoryJsonBtn">
                        <em class="icon ni ni-check me-1"></em> Apply JSON to Form
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // CKEditor helper functions
        function initCKEditorForElement(element) {
            if (typeof ClassicEditor === 'undefined' || !element || element.classList.contains('ckeditor-initialized')) return;
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

        function setCkeditorContent(element, content) {
            if (!element) return;
            element.value = content || '';
            if (element.ckeditorInstance) {
                element.ckeditorInstance.setData(content || '');
            }
        }

        let catSectionCounter = {{ isset($text_sections) && is_array($text_sections) ? count($text_sections) : 0 }};
        let newCriteriaIndex = 0;

        document.addEventListener('DOMContentLoaded', function() {
            // Category Type Toggle
            function updateCategoryTypeUI() {
                const parentRadio = document.getElementById('type_parent');
                const parentCategoryGroup = document.getElementById('parent_category_group');
                const parentCategorySelect = document.getElementById('parent_id');
                const cardParent = document.getElementById('card_type_parent');
                const cardSub = document.getElementById('card_type_subcategory');

                const isParent = parentRadio ? parentRadio.checked : true;
                if (isParent) {
                    if (parentCategoryGroup) {
                        parentCategoryGroup.style.display = 'none';
                    }
                    if (parentCategorySelect) {
                        parentCategorySelect.value = '';
                    }
                    if (cardParent) {
                        cardParent.classList.add('border-primary', 'bg-primary-dim');
                        cardParent.classList.remove('bg-white');
                    }
                    if (cardSub) {
                        cardSub.classList.remove('border-info', 'bg-info-dim');
                        cardSub.classList.add('bg-white');
                    }
                } else {
                    if (parentCategoryGroup) {
                        parentCategoryGroup.style.display = 'block';
                    }
                    if (cardSub) {
                        cardSub.classList.add('border-info', 'bg-info-dim');
                        cardSub.classList.remove('bg-white');
                    }
                    if (cardParent) {
                        cardParent.classList.remove('border-primary', 'bg-primary-dim');
                        cardParent.classList.add('bg-white');
                    }
                }
            }

            document.querySelectorAll('input[name="is_parent"]').forEach(function(radio) {
                radio.addEventListener('change', updateCategoryTypeUI);
            });
            updateCategoryTypeUI();

            // Comparison Slug Auto-generate & Pencil Edit Toggle
            const nameInput = document.getElementById('name');
            const catCapInput = document.getElementById('category_cap');
            if (catCapInput && nameInput) {
                catCapInput.addEventListener('input', function() {
                    nameInput.value = this.value;
                    updateComparisonSlug();
                });
            }
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

            function appendCriteriaCard(name, description) {
                newCriteriaIndex++;
                const card = document.createElement('div');
                card.className = 'criteria-card border p-3 rounded mb-2 bg-white position-relative';
                const safeName = name ? $('<div>').text(name).html() : '';
                const safeDesc = description ? $('<div>').text(description).html() : '';
                card.innerHTML = `
                    <div class="row g-2 align-items-center">
                        <div class="col-md-5">
                            <label class="form-label mb-1 small fw-bold">Criteria Name</label>
                            <input type="text" class="form-control" name="new_rating_criteria[${newCriteriaIndex}][name]" value="${safeName}" placeholder="e.g. Performance, Scalability" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label mb-1 small fw-bold">Short Description Text</label>
                            <input type="text" class="form-control" name="new_rating_criteria[${newCriteriaIndex}][description]" value="${safeDesc}" placeholder="Description shown below criteria in review modal" />
                        </div>
                        <div class="col-md-1 text-end">
                            <label class="form-label mb-1 d-block opacity-0">Remove</label>
                            <button type="button" class="btn btn-outline-danger remove-criteria"><i class="fa fa-trash"></i></button>
                        </div>
                    </div>
                `;
                criteriaWrapper.appendChild(card);
            }

            if (addCriteriaBtn && criteriaWrapper) {
                addCriteriaBtn.addEventListener('click', function() {
                    appendCriteriaCard('', '');
                });

                criteriaWrapper.addEventListener('click', function(e) {
                    const removeBtn = e.target.closest('.remove-criteria');
                    if (removeBtn) {
                        removeBtn.closest('.criteria-card').remove();
                    }
                });
            }

            window.appendCriteriaCard = appendCriteriaCard;
        });

        // Initialize CKEditors on load
        initAllCKEditors();

        // Dynamic Text Section (H2) and Sub-sections (H3) handlers
        $('#addCategoryTextSectionBtn').on('click', function() {
            let sIdx = catSectionCounter++;
            let currentCount = $('#categoryTextSectionsContainer .section-card').length + 1;
            let secHtml = `
            <div class="section-card border rounded p-3 mb-3 bg-white" data-section-index="${sIdx}">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">Section ${currentCount}</h6>
                    <button type="button" class="btn btn-sm btn-danger remove-section-btn">Delete Section</button>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">Headline (H2)</label>
                        <input type="text" class="form-control" name="text_sections[${sIdx}][h2_title]" placeholder="e.g. About web hosting">
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
            $('#categoryTextSectionsContainer').append(secHtml);
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
            $('#categoryTextSectionsContainer .section-card').each(function(i) {
                $(this).find('h6').first().text('Section ' + (i + 1));
            });
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

        // Ensure CKEditor data syncs on form submit
        $('form.form-validate').on('submit', function() {
            document.querySelectorAll('textarea.rich-editor').forEach(el => {
                if (el.ckeditorInstance) {
                    el.value = el.ckeditorInstance.getData();
                }
            });
        });

        // ==========================================
        // JSON IMPORT & EXPORT SYSTEM
        // ==========================================
        const sampleCategoryJson = {
            "category_type": "parent",
            "is_parent": true,
            "category_cap": "Web Hosting",
            "category_tc": "Web Hosting",
            "category_lc": "web hosting",
            "entity1_cap": "Web hosting provider",
            "entity1_lc": "web hosting provider",
            "entities1_cap": "Web hosting providers",
            "entities1_lc": "web hosting providers",
            "entity1_short_cap": "Provider",
            "entity1_short_lc": "provider",
            "entities1_short_cap": "Providers",
            "entities1_short_lc": "providers",
            "entity2_cap": "Web hosting service",
            "entity2_lc": "web hosting service",
            "entities2_cap": "Web hosting services",
            "entities2_lc": "web hosting services",
            "entity2_short_cap": "Service",
            "entity2_short_lc": "service",
            "entities2_short_cap": "Services",
            "entities2_short_lc": "services",
            "page_title": "Web Hosting Comparison",
            "page_title_addition": "[country2] ([year])",
            "comparison_slug": "web-hosting-comparison",
            "glance_option_3_label": "Hosting options",
            "glance_option_4_label": "Website tools",
            "description": "<p>Discover and compare top web hosting solutions with independent ratings, verified user reviews, and comprehensive server benchmarks.</p>",
            "is_important": true,
            "category_overlay_position": "right",
            "faq_overlay_position": "right",
            "comparison_overlay_position": "right",
            "default_rating_criteria": {
                "pricing_value": "Value for money and pricing flexibility",
                "features_functionality": "Range of features, uptime guarantee, and server tooling",
                "ease_of_use": "Control panel interface and setup simplicity"
            },
            "custom_rating_criteria": [
                {
                    "name": "Server Speed & Uptime",
                    "description": "Average response time, TTFB, and uptime reliability"
                },
                {
                    "name": "Customer Support",
                    "description": "24/7 technical support responsiveness and knowledge"
                }
            ],
            "text_sections": [
                {
                    "h2_title": "How to Choose the Best Web Hosting",
                    "h2_text": "<p>When selecting a web host, evaluate your expected traffic, required server resources, and database options.</p>",
                    "sub_sections": [
                        {
                            "h3_title": "What is the difference between Shared and VPS hosting?",
                            "h3_text": "<p>Shared hosting shares server resources among multiple tenants, whereas VPS provides isolated virtual resources.</p>"
                        }
                    ]
                }
            ]
        };

        const sampleSubcategoryJson = {
            "category_type": "subcategory",
            "is_parent": false,
            "parent_name": "Web Hosting",
            "category_cap": "Cloud Hosting",
            "category_tc": "Cloud Hosting",
            "category_lc": "cloud hosting",
            "entity1_cap": "Cloud hosting provider",
            "entity1_lc": "cloud hosting provider",
            "entities1_cap": "Cloud hosting providers",
            "entities1_lc": "cloud hosting providers",
            "entity1_short_cap": "Provider",
            "entity1_short_lc": "provider",
            "entities1_short_cap": "Providers",
            "entities1_short_lc": "providers",
            "entity2_cap": "Cloud hosting service",
            "entity2_lc": "cloud hosting service",
            "entities2_cap": "Cloud hosting services",
            "entities2_lc": "cloud hosting services",
            "entity2_short_cap": "Service",
            "entity2_short_lc": "service",
            "entities2_short_cap": "Services",
            "entities2_short_lc": "services",
            "page_title": "Cloud Hosting Comparison",
            "page_title_addition": "[country2] ([year])",
            "comparison_slug": "cloud-hosting-comparison",
            "glance_option_3_label": "Cloud architecture",
            "glance_option_4_label": "Scalability options",
            "description": "<p>Compare scalable cloud hosting infrastructure, elastic server pricing, and high-performance cloud clusters.</p>",
            "is_important": false,
            "category_overlay_position": "right",
            "faq_overlay_position": "right",
            "comparison_overlay_position": "right",
            "custom_rating_criteria": [
                {
                    "name": "Elastic Scalability",
                    "description": "Auto-scaling capabilities during high-traffic surges"
                }
            ],
            "text_sections": [
                {
                    "h2_title": "Understanding Cloud Infrastructure",
                    "h2_text": "<p>Cloud hosting leverages distributed virtual networks to guarantee high availability and fault tolerance.</p>",
                    "sub_sections": []
                }
            ]
        };

        function closeCategoryJsonModal() {
            $('#categoryJsonModal').modal('hide');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css({
                'overflow': '',
                'padding-right': ''
            });
        }

        $(document).on('hidden.bs.modal', '#categoryJsonModal', function () {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css({
                'overflow': '',
                'padding-right': ''
            });
        });

        $(document).on('click', '#categoryJsonModal [data-dismiss="modal"]', function(e) {
            e.preventDefault();
            closeCategoryJsonModal();
        });

        // Modal Action Buttons
        $('#loadSampleCategoryJsonBtn').on('click', function() {
            $('#categoryJsonInput').val(JSON.stringify(sampleCategoryJson, null, 2));
            $('#categoryJsonError').hide();
            $('#categoryJsonSuccess').hide();
        });

        $('#loadSampleSubcategoryJsonBtn').on('click', function() {
            $('#categoryJsonInput').val(JSON.stringify(sampleSubcategoryJson, null, 2));
            $('#categoryJsonError').hide();
            $('#categoryJsonSuccess').hide();
        });

        $('#clearJsonModalBtn').on('click', function() {
            $('#categoryJsonInput').val('');
            $('#categoryJsonError').hide();
            $('#categoryJsonSuccess').hide();
        });

        $('#copySampleJsonBtn').on('click', function() {
            const currentVal = $('#categoryJsonInput').val() || JSON.stringify(sampleCategoryJson, null, 2);
            navigator.clipboard.writeText(currentVal).then(function() {
                if (typeof toastr !== 'undefined') {
                    toastr.success('JSON copied to clipboard!');
                } else {
                    alert('JSON copied to clipboard!');
                }
            });
        });

        // Apply JSON to Form
        $('#applyCategoryJsonBtn').on('click', function() {
            const rawJson = $('#categoryJsonInput').val().trim();
            const errorDiv = $('#categoryJsonError');
            const successDiv = $('#categoryJsonSuccess');

            errorDiv.hide().text('');
            successDiv.hide().text('');

            if (!rawJson) {
                errorDiv.text('Please paste JSON data in the box above.').show();
                return;
            }

            let data;
            try {
                data = JSON.parse(rawJson);
            } catch (err) {
                errorDiv.text('Invalid JSON format: ' + err.message).show();
                return;
            }

            if (typeof data !== 'object' || data === null || Array.isArray(data)) {
                errorDiv.text('Invalid JSON structure: Expected a single JSON object with category fields.').show();
                return;
            }

            try {
                // 1. Category Name Placeholders
                const catCap = data.category_cap || data.name || data.category_name || '';
                if (catCap) {
                    $('#category_cap').val(catCap);
                    $('#name').val(catCap);
                }
                if (data.category_tc !== undefined) $('#category_tc').val(data.category_tc);
                if (data.category_lc !== undefined) $('#category_lc').val(data.category_lc);

                // 2. Entity 1 Placeholders
                if (data.entity1_cap !== undefined) $('#entity1_cap').val(data.entity1_cap);
                if (data.entity1_lc !== undefined) $('#entity1_lc').val(data.entity1_lc);
                if (data.entities1_cap !== undefined) $('#entities1_cap').val(data.entities1_cap);
                if (data.entities1_lc !== undefined) $('#entities1_lc').val(data.entities1_lc);
                if (data.entity1_short_cap !== undefined) $('#entity1_short_cap').val(data.entity1_short_cap);
                if (data.entity1_short_lc !== undefined) $('#entity1_short_lc').val(data.entity1_short_lc);
                if (data.entities1_short_cap !== undefined) $('#entities1_short_cap').val(data.entities1_short_cap);
                if (data.entities1_short_lc !== undefined) $('#entities1_short_lc').val(data.entities1_short_lc);

                // 3. Entity 2 Placeholders
                if (data.entity2_cap !== undefined) $('#entity2_cap').val(data.entity2_cap);
                if (data.entity2_lc !== undefined) $('#entity2_lc').val(data.entity2_lc);
                if (data.entities2_cap !== undefined) $('#entities2_cap').val(data.entities2_cap);
                if (data.entities2_lc !== undefined) $('#entities2_lc').val(data.entities2_lc);
                if (data.entity2_short_cap !== undefined) $('#entity2_short_cap').val(data.entity2_short_cap);
                if (data.entity2_short_lc !== undefined) $('#entity2_short_lc').val(data.entity2_short_lc);
                if (data.entities2_short_cap !== undefined) $('#entities2_short_cap').val(data.entities2_short_cap);
                if (data.entities2_short_lc !== undefined) $('#entities2_short_lc').val(data.entities2_short_lc);

                // 4. Page Title, Addition & Comparison Slug
                if (data.page_title !== undefined) $('#page_title').val(data.page_title);
                if (data.page_title_addition !== undefined) $('#page_title_addition').val(data.page_title_addition);
                if (data.comparison_slug !== undefined) {
                    $('#comparison_slug').val(data.comparison_slug);
                } else if (catCap && !$('#comparison_slug').val()) {
                    const genSlug = catCap.toLowerCase().trim().replace(/&/g, '-and-').replace(/[\s\W-]+/g, '-').replace(/^-+|-+$/g, '');
                    $('#comparison_slug').val(genSlug ? genSlug + '-comparison' : '');
                }

                // 5. At a glance labels
                const g3 = data.glance_option_3_label || data.option_3_label || data.option_3 || '';
                const g4 = data.glance_option_4_label || data.option_4_label || data.option_4 || '';
                if (g3) $('#glance_option_3_label').val(g3);
                if (g4) $('#glance_option_4_label').val(g4);

                // 6. Header description (rich editor)
                const desc = data.description || data.header_text || '';
                if (desc) {
                    setCkeditorContent(document.getElementById('description'), desc);
                }

                // 7. Category Type (Parent vs Subcategory)
                let isParent = true;
                if (data.category_type) {
                    isParent = (data.category_type.toLowerCase() === 'parent' || data.category_type.toLowerCase() === 'category');
                } else if (data.is_parent !== undefined) {
                    isParent = Boolean(data.is_parent);
                } else if (data.parent_id || data.parent_name || data.parent_category) {
                    isParent = false;
                }

                if (isParent) {
                    $('#type_parent').prop('checked', true).trigger('change');
                } else {
                    $('#type_subcategory').prop('checked', true).trigger('change');

                    // Match parent category by ID or name
                    const targetParentId = data.parent_id;
                    const targetParentName = (data.parent_name || data.parent_category || '').toString().toLowerCase().trim();

                    if (targetParentId) {
                        $('#parent_id').val(targetParentId);
                    } else if (targetParentName) {
                        let matchedVal = '';
                        $('#parent_id option').each(function() {
                            const optText = $(this).text().toLowerCase().trim();
                            if (optText === targetParentName || optText.includes(targetParentName)) {
                                matchedVal = $(this).val();
                                return false;
                            }
                        });
                        if (matchedVal) {
                            $('#parent_id').val(matchedVal);
                        }
                    }
                }

                // 8. Important Category
                if (data.is_important !== undefined) {
                    $('#is_important').prop('checked', Boolean(data.is_important));
                }

                // 9. Overlay Positions
                if (data.category_overlay_position) $('#category_overlay_position').val(data.category_overlay_position);
                if (data.faq_overlay_position) $('#faq_overlay_position').val(data.faq_overlay_position);
                if (data.comparison_overlay_position) $('#comparison_overlay_position').val(data.comparison_overlay_position);

                // 10. Default Rating Criteria
                if (data.default_rating_criteria && typeof data.default_rating_criteria === 'object') {
                    for (const [key, descVal] of Object.entries(data.default_rating_criteria)) {
                        const targetInput = $(`input[name="default_rating_criteria[${key}][description]"]`);
                        if (targetInput.length) {
                            targetInput.val(descVal);
                        }
                    }
                }

                // 11. Custom Rating Criteria
                const customCriteriaList = data.custom_rating_criteria || data.new_rating_criteria || data.criteria;
                if (Array.isArray(customCriteriaList) && customCriteriaList.length > 0) {
                    $('#criteria_wrapper .criteria-card').remove();
                    customCriteriaList.forEach(item => {
                        const cName = typeof item === 'string' ? item : (item.name || item.title || '');
                        const cDesc = typeof item === 'object' && item !== null ? (item.description || item.desc || '') : '';
                        if (cName && window.appendCriteriaCard) {
                            window.appendCriteriaCard(cName, cDesc);
                        }
                    });
                }

                // 12. Dynamic Text Sections (H2 & H3)
                const textSecs = data.text_sections || data.sections;
                if (Array.isArray(textSecs) && textSecs.length > 0) {
                    // Destroy existing CKEditors
                    $('#categoryTextSectionsContainer').find('textarea.rich-editor').each(function() {
                        if (this.ckeditorInstance) {
                            this.ckeditorInstance.destroy();
                        }
                    });
                    $('#categoryTextSectionsContainer').empty();
                    catSectionCounter = 0;

                    textSecs.forEach((sec, sIdx) => {
                        let currentCount = sIdx + 1;
                        let secHtml = `
                        <div class="section-card border rounded p-3 mb-3 bg-white" data-section-index="${sIdx}">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <h6 class="fw-bold mb-0 text-dark">Section ${currentCount}</h6>
                                <button type="button" class="btn btn-sm btn-danger remove-section-btn">Delete Section</button>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Headline (H2)</label>
                                    <input type="text" class="form-control" name="text_sections[${sIdx}][h2_title]" value="${$('<div>').text(sec.h2_title || '').html()}" placeholder="e.g. About web hosting">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Headline Content (Rich Editor)</label>
                                    <textarea class="form-control rich-editor" rows="4" name="text_sections[${sIdx}][h2_text]" placeholder="Enter main content for this H2 section...">${$('<div>').text(sec.h2_text || '').html()}</textarea>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-bold text-secondary mb-0">Sub-sections (H3 Sub-headlines)</label>
                                        <button type="button" class="btn btn-xs btn-outline-primary add-sub-section-btn" data-section-index="${sIdx}">+ Add Sub-headline (H3)</button>
                                    </div>
                                    <div class="sub-sections-container ps-3 border-start">`;

                        const subList = sec.sub_sections || sec.subsections || [];
                        if (Array.isArray(subList)) {
                            subList.forEach((sub, subIdx) => {
                                secHtml += `
                                <div class="sub-section-item border rounded p-2 mb-2 bg-light" data-sub-index="${subIdx}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small fw-bold text-muted">Sub-headline (H3)</span>
                                        <button type="button" class="btn btn-xs btn-outline-danger remove-sub-btn">Remove</button>
                                    </div>
                                    <input type="text" class="form-control form-control-sm mb-2" name="text_sections[${sIdx}][sub_sections][${subIdx}][h3_title]" value="${$('<div>').text(sub.h3_title || '').html()}" placeholder="e.g. What is web hosting?">
                                    <textarea class="form-control rich-editor" rows="3" name="text_sections[${sIdx}][sub_sections][${subIdx}][h3_text]" placeholder="Enter sub-headline content...">${$('<div>').text(sub.h3_text || '').html()}</textarea>
                                </div>`;
                            });
                        }

                        secHtml += `</div>
                                </div>
                            </div>
                        </div>`;
                        $('#categoryTextSectionsContainer').append(secHtml);
                        catSectionCounter = sIdx + 1;
                    });
                    initAllCKEditors();
                }

                successDiv.text('Category details successfully applied to the form!').show();

                setTimeout(function() {
                    closeCategoryJsonModal();

                    if (typeof toastr !== 'undefined') {
                        toastr.success('Category details populated from JSON!');
                    }
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }, 300);

            } catch (fillErr) {
                console.error("Fill error:", fillErr);
                errorDiv.text('Error populating form: ' + fillErr.message).show();
            }
        });

        // Export Current Form as JSON
        $('#exportCategoryJsonBtn').on('click', function() {
            // Sync all CKEditors before reading
            document.querySelectorAll('textarea.rich-editor').forEach(el => {
                if (el.ckeditorInstance) {
                    el.value = el.ckeditorInstance.getData();
                }
            });

            const isParent = $('input[name="is_parent"]:checked').val() === '1';
            const selectedParentId = $('#parent_id').val();
            const selectedParentText = selectedParentId ? $('#parent_id option:selected').text().trim() : '';

            // Default criteria map
            const defCriteriaMap = {};
            $('input[name^="default_rating_criteria"]').each(function() {
                const match = $(this).attr('name').match(/default_rating_criteria\[([^\]]+)\]\[description\]/);
                if (match && match[1]) {
                    defCriteriaMap[match[1]] = $(this).val();
                }
            });

            // Custom criteria list
            const customCriteria = [];
            $('#criteria_wrapper .criteria-card').each(function() {
                const nameInput = $(this).find('input[name*="[name]"]');
                const descInput = $(this).find('input[name*="[description]"]');
                if (nameInput.val()) {
                    customCriteria.push({
                        name: nameInput.val(),
                        description: descInput.val() || ''
                    });
                }
            });

            // Dynamic text sections
            const textSectionsList = [];
            $('#categoryTextSectionsContainer .section-card').each(function() {
                const sIdx = $(this).data('section-index');
                const h2Title = $(this).find(`input[name="text_sections[${sIdx}][h2_title]"]`).val() || '';
                const h2Text = $(this).find(`textarea[name="text_sections[${sIdx}][h2_text]"]`).val() || '';
                const subSections = [];

                $(this).find('.sub-section-item').each(function() {
                    const subIdx = $(this).data('sub-index');
                    const h3Title = $(this).find(`input[name="text_sections[${sIdx}][sub_sections][${subIdx}][h3_title]"]`).val() || '';
                    const h3Text = $(this).find(`textarea[name="text_sections[${sIdx}][sub_sections][${subIdx}][h3_text]"]`).val() || '';
                    if (h3Title || h3Text) {
                        subSections.push({
                            h3_title: h3Title,
                            h3_text: h3Text
                        });
                    }
                });

                if (h2Title || h2Text || subSections.length > 0) {
                    textSectionsList.push({
                        h2_title: h2Title,
                        h2_text: h2Text,
                        sub_sections: subSections
                    });
                }
            });

            const exportedData = {
                category_type: isParent ? 'parent' : 'subcategory',
                is_parent: isParent,
                parent_id: isParent ? null : (selectedParentId ? parseInt(selectedParentId) : null),
                parent_name: isParent ? null : selectedParentText,
                is_important: $('#is_important').is(':checked'),
                category_cap: $('#category_cap').val() || '',
                category_tc: $('#category_tc').val() || '',
                category_lc: $('#category_lc').val() || '',
                entity1_cap: $('#entity1_cap').val() || '',
                entity1_lc: $('#entity1_lc').val() || '',
                entities1_cap: $('#entities1_cap').val() || '',
                entities1_lc: $('#entities1_lc').val() || '',
                entity1_short_cap: $('#entity1_short_cap').val() || '',
                entity1_short_lc: $('#entity1_short_lc').val() || '',
                entities1_short_cap: $('#entities1_short_cap').val() || '',
                entities1_short_lc: $('#entities1_short_lc').val() || '',
                entity2_cap: $('#entity2_cap').val() || '',
                entity2_lc: $('#entity2_lc').val() || '',
                entities2_cap: $('#entities2_cap').val() || '',
                entities2_lc: $('#entities2_lc').val() || '',
                entity2_short_cap: $('#entity2_short_cap').val() || '',
                entity2_short_lc: $('#entity2_short_lc').val() || '',
                entities2_short_cap: $('#entities2_short_cap').val() || '',
                entities2_short_lc: $('#entities2_short_lc').val() || '',
                page_title: $('#page_title').val() || '',
                page_title_addition: $('#page_title_addition').val() || '',
                comparison_slug: $('#comparison_slug').val() || '',
                glance_option_3_label: $('#glance_option_3_label').val() || '',
                glance_option_4_label: $('#glance_option_4_label').val() || '',
                description: $('#description').val() || '',
                category_overlay_position: $('#category_overlay_position').val() || 'right',
                faq_overlay_position: $('#faq_overlay_position').val() || 'right',
                comparison_overlay_position: $('#comparison_overlay_position').val() || 'right',
                default_rating_criteria: defCriteriaMap,
                custom_rating_criteria: customCriteria,
                text_sections: textSectionsList
            };

            const jsonStr = JSON.stringify(exportedData, null, 2);
            $('#categoryJsonInput').val(jsonStr);

            navigator.clipboard.writeText(jsonStr).then(function() {
                if (typeof toastr !== 'undefined') {
                    toastr.success('Form data exported and copied to clipboard as JSON!');
                } else {
                    alert('Form data exported and copied to clipboard as JSON!');
                }
            }).catch(function() {
                if (typeof toastr !== 'undefined') {
                    toastr.info('JSON generated. Opening modal...');
                }
            });

            // Open modal to view/edit JSON
            $('#categoryJsonModal').modal('show');
        });
    </script>
@endsection
