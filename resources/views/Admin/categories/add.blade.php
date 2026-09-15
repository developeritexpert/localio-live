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
                                <label class="form-label" for="see_all_text">"Explore [business]" Link Text</label>
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
                                        value="{{ old('page_title_addition', $category->page_title_addition ?? ($category_data['category']['page_title_addition'] ?? '')) }}" 
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
                                <label class="form-label" for="description">Header Text</label>
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
                            <div class="form-group">
                                <label class="form-label" for="top_rated_listing_headline">Compare Top-Rated Headline (H2)</label>
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
                                <label class="form-label" for="top_rated_listing_headline_addition">Compare Top-Rated Headline (H2) addition (no translate)</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="top_rated_listing_headline_addition" name="top_rated_listing_headline_addition"
                                        value="{{ old('top_rated_listing_headline_addition', $category->top_rated_listing_headline_addition ?? ($category_data['category']['top_rated_listing_headline_addition'] ?? '')) }}"
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
                                <label class="form-label" for="top_rated_listing_subtext">Compare Top-Rated Subtext</label>
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
                                <label class="form-label fw-bold" for="compare_alternatives_headline">Top 10 Headline (H2)</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="compare_alternatives_headline" name="compare_alternatives_headline"
                                        value="{{ isset($category_data) ? ($category_data['compare_alternatives_headline'] ?? '') : old('compare_alternatives_headline') }}"
                                        placeholder="e.g. Compare [Category] alternatives">
                                </div>
                                <small class="text-muted d-block mt-1">Headline for the Compare Alternatives table section on the category page. Defaults to "Compare {Category} alternatives" if left empty.</small>
                                @error('compare_alternatives_headline')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="compare_alternatives_subtext">Top 10 Subtext</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control rich-editor" id="compare_alternatives_subtext" name="compare_alternatives_subtext" rows="3" placeholder="Enter subtext displayed below the Compare Alternatives headline">{!! isset($category_data) ? ($category_data['compare_alternatives_subtext'] ?? '') : old('compare_alternatives_subtext') !!}</textarea>
                                </div>
                                <small class="text-muted d-block mt-1">Short text placed immediately below the Compare Alternatives headline. Defaults to empty if not provided.</small>
                                @error('compare_alternatives_subtext')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="community_says_headline">Pros and Cons Headline (H2)</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="community_says_headline" name="community_says_headline"
                                        value="{{ isset($category_data) ? ($category_data['community_says_headline'] ?? '') : old('community_says_headline') }}"
                                        placeholder="e.g. What the Localio community says">
                                </div>
                                <small class="text-muted d-block mt-1">Headline for the community pros and cons feedback section on the category page. Defaults to "What the Localio community says" if left empty.</small>
                                @error('community_says_headline')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="community_says_subtext">Pros and Cons Subtext</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control rich-editor" id="community_says_subtext" name="community_says_subtext" rows="3" placeholder="Enter subtext displayed below the What the community says headline">{!! isset($category_data) ? ($category_data['community_says_subtext'] ?? '') : old('community_says_subtext') !!}</textarea>
                                </div>
                                <small class="text-muted d-block mt-1">Short text placed immediately below the Community Says headline. Defaults to "Most mentioned pros and cons shared by real users." if left empty.</small>
                                @error('community_says_subtext')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>



                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="popular_comparisons_headline">Popular Comparisons Headline (H2)</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="popular_comparisons_headline" name="popular_comparisons_headline"
                                        value="{{ isset($category_data) ? ($category_data['popular_comparisons_headline'] ?? '') : old('popular_comparisons_headline') }}"
                                        placeholder="e.g. Popular comparisons">
                                </div>
                                <small class="text-muted d-block mt-1">Headline for the Popular Comparisons section on the category page. Defaults to "Popular comparisons" if left empty.</small>
                                @error('popular_comparisons_headline')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="popular_comparisons_subtext">Popular Comparisons Subtext</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control rich-editor" id="popular_comparisons_subtext" name="popular_comparisons_subtext" rows="3" placeholder="Enter subtext displayed below the Popular Comparisons headline">{!! isset($category_data) ? ($category_data['popular_comparisons_subtext'] ?? '') : old('popular_comparisons_subtext') !!}</textarea>
                                </div>
                                <small class="text-muted d-block mt-1">Short text placed immediately below the Popular Comparisons headline on the category page. Defaults to "See how top [category] providers compare against each other." if left empty.</small>
                                @error('popular_comparisons_subtext')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="compare_tool_headline">Comparison Tool Headline (H2)</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="compare_tool_headline" name="compare_tool_headline"
                                        value="{{ isset($category_data) ? ($category_data['compare_tool_headline'] ?? '') : old('compare_tool_headline') }}"
                                        placeholder="e.g. Compare {category} with any provider">
                                </div>
                                <small class="text-muted d-block mt-1">Headline for the interactive provider comparison box on the category page. Defaults to "Compare [Category] with any provider" if left empty.</small>
                                @error('compare_tool_headline')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="compare_tool_subtext">Comparison Tool Subtext</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control rich-editor" id="compare_tool_subtext" name="compare_tool_subtext" rows="3" placeholder="Enter subtext displayed below the Comparison Tool headline">{!! isset($category_data) ? ($category_data['compare_tool_subtext'] ?? '') : old('compare_tool_subtext') !!}</textarea>
                                </div>
                                <small class="text-muted d-block mt-1">Short explanatory text displayed below the comparison tool headline. Defaults to "Select another provider from the search box below to generate an instant side-by-side comparison." if left empty.</small>
                                @error('compare_tool_subtext')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- move explore sub categroy section here -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="explore_subcategories_headline">Explore Subcategories Headline (H2)</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="explore_subcategories_headline" name="explore_subcategories_headline"
                                        value="{{ isset($category_data) ? ($category_data['explore_subcategories_headline'] ?? '') : old('explore_subcategories_headline') }}"
                                        placeholder="e.g. Explore [Category] categories">
                                </div>
                                <small class="text-muted d-block mt-1">Headline for the explore subcategories/categories section on the category page. Defaults to "Explore [Category] categories" if left empty.</small>
                                @error('explore_subcategories_headline')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="explore_subcategories_subtext">Explore Subcategories Subtext</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control rich-editor" id="explore_subcategories_subtext" name="explore_subcategories_subtext" rows="3" placeholder="Enter subtext displayed below the Explore subcategories headline">{{ isset($category_data) ? ($category_data['explore_subcategories_subtext'] ?? '') : old('explore_subcategories_subtext') }}</textarea>
                                </div>
                                <small class="text-muted d-block mt-1">Short text placed immediately below the Explore subcategories headline. Defaults to "Browse specialized categories to find the exact tools and solutions for your requirements." if left empty.</small>
                                @error('explore_subcategories_subtext')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- move explore sub categroy section end here -->

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="category_faqs_headline">Category Page FAQs Headline (H2)</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="category_faqs_headline" name="category_faqs_headline"
                                        value="{{ isset($category_data) ? ($category_data['category_faqs_headline'] ?? '') : old('category_faqs_headline') }}"
                                        placeholder="e.g. Frequently asked questions">
                                </div>
                                <small class="text-muted d-block mt-1">Headline for the FAQ section at the bottom of the main Category page. Defaults to "Frequently asked questions" if left empty.</small>
                                @error('category_faqs_headline')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label fw-bold" for="category_faqs_subtext">Category Page FAQs Subtext</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control rich-editor" id="category_faqs_subtext" name="category_faqs_subtext" rows="3" placeholder="Enter subtext displayed below the Category Page FAQ headline">{!! isset($category_data) ? ($category_data['category_faqs_subtext'] ?? '') : old('category_faqs_subtext') !!}</textarea>
                                </div>
                                <small class="text-muted d-block mt-1">Short text placed immediately below the FAQ headline on the main Category page. Defaults to "Common questions and answers about [category] solutions and comparison criteria." if left empty.</small>
                                @error('category_faqs_subtext')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
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
                                                <label class="form-label fw-bold" for="faq_about_headline">FAQ Page Second Headline (H2)</label>
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

                                        <!-- FAQ Page About Content with CKEditor -->
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label fw-bold" for="faq_about_description">FAQ Page Content Text</label>
                                                <div class="form-control-wrap">
                                                    <textarea class="form-control rich-editor" id="faq_about_description" name="faq_about_description" rows="5" placeholder="Enter introductory text displayed below the FAQ About headline...">{!! isset($category_data) ? ($category_data['faq_about_description'] ?? '') : old('faq_about_description') !!}</textarea>
                                                </div>
                                                <small class="text-muted d-block mt-1">Rich-text content displayed below the FAQ "About" headline on the FAQ page.</small>
                                                @error('faq_about_description')
                                                    <div class="error text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- FAQ Page Explore Subcategories Headline -->
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label fw-bold" for="faq_explore_headline">FAQ Explore Categories Headline (H2)</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="faq_explore_headline" name="faq_explore_headline"
                                                        value="{{ isset($category_data) ? ($category_data['faq_explore_headline'] ?? '') : old('faq_explore_headline') }}"
                                                        placeholder="e.g. Explore Web hosting categories">
                                                </div>
                                                <small class="text-muted d-block mt-1">Headline displayed above the explore subcategories section on the FAQ page. Defaults to "Explore [Category Name] categories".</small>
                                                @error('faq_explore_headline')
                                                    <div class="error text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- FAQ Page Explore Subcategories Subtext -->
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label fw-bold" for="faq_explore_subtext">FAQ Explore Categories Subtext</label>
                                                <div class="form-control-wrap">
                                                    <textarea class="form-control rich-editor" id="faq_explore_subtext" name="faq_explore_subtext" rows="3" placeholder="Browse specialized categories to find the exact tools and solutions for your requirements.">{!! isset($category_data) ? ($category_data['faq_explore_subtext'] ?? '') : old('faq_explore_subtext') !!}</textarea>
                                                </div>
                                                <small class="text-muted d-block mt-1">Rich-text description placed below the Explore Categories headline on the FAQ page. Defaults to standard subtext if left empty.</small>
                                                @error('faq_explore_subtext')
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