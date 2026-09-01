<?php
$filePath = '/home/ubuntu/projects/localio-live/resources/views/Admin/categories/add.blade.php';
$content = file_get_contents($filePath);

$faqAdminSection = <<<HTML

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
                                                        value="{{ isset(\$category_data) ? (\$category_data['faq_h1_title'] ?? '') : old('faq_h1_title') }}"
                                                        placeholder="e.g. Web Hosting FAQs">
                                                </div>
                                                <small class="text-muted d-block mt-1">Main H1 headline shown at the top of the FAQ page. Defaults to "[Category Name] FAQs" if left empty.</small>
                                                @error('faq_h1_title')
                                                    <div class="error text-danger">{{ \$message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- FAQ Page Subtext below H1 with CKEditor -->
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label fw-bold" for="faq_subtext">FAQ Page Subtext (Below H1)</label>
                                                <div class="form-control-wrap">
                                                    <textarea class="form-control rich-editor" id="faq_subtext" name="faq_subtext" rows="3" placeholder="Frequently asked questions and answers about this category on Localio.">{!! isset(\$category_data) ? (\$category_data['faq_subtext'] ?? '') : old('faq_subtext') !!}</textarea>
                                                </div>
                                                <small class="text-muted d-block mt-1">Rich-text description placed directly underneath the FAQ Page H1 title. Defaults to standard intro text if left empty.</small>
                                                @error('faq_subtext')
                                                    <div class="error text-danger">{{ \$message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- FAQ Page SVG Graphic Upload -->
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label fw-bold" for="faq_image">FAQ Page SVG Graphic (Top Right)</label>
                                                <div class="form-control-wrap">
                                                    <div class="form-file">
                                                        <input type="file" class="form-file-input" id="faq_image" name="faq_image" accept="image/svg+xml">
                                                        <label class="form-file-label" for="faq_image">Choose SVG file</label>
                                                    </div>
                                                    @if(isset(\$category) && \$category->faq_image)
                                                        <div class="mt-2">
                                                            <span class="badge bg-outline-info">Current FAQ graphic uploaded</span>
                                                            @if(\$category->faqImageMedia)
                                                                <img src="{{ asset(\$category->faqImageMedia->dir_path . '/' . \$category->faqImageMedia->file_name) }}" alt="FAQ Graphic" class="mt-2 p-1 bg-white border rounded" style="max-height: 60px; display: block;">
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                                <small class="text-muted d-block mt-1">SVG illustration displayed on the right top header of the FAQ page for this category.</small>
                                                @error('faq_image')
                                                    <div class="error text-danger">{{ \$message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- FAQ Page About Headline (H2) -->
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label fw-bold" for="faq_about_headline">FAQ Page "About" Headline (H2)</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="faq_about_headline" name="faq_about_headline"
                                                        value="{{ isset(\$category_data) ? (\$category_data['faq_about_headline'] ?? '') : old('faq_about_headline') }}"
                                                        placeholder="e.g. About Web hosting">
                                                </div>
                                                <small class="text-muted d-block mt-1">Headline displayed above the introductory text in the FAQ content section. Defaults to "About [Category Name]".</small>
                                                @error('faq_about_headline')
                                                    <div class="error text-danger">{{ \$message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
HTML;

$search = <<<HTML
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="top_rated_description">Top-Rated Page Description</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control rich-editor" id="top_rated_description" name="top_rated_description" rows="4" placeholder="Enter SEO-optimized short description displayed in the 'Explore top-rated categories' section on the Top Rated page">{!! isset(\$category_data) ? (\$category_data['top_rated_description'] ?? '') : old('top_rated_description') !!}</textarea>
                                </div>
                                <small class="text-muted d-block mt-1">Specific description for the Top-Rated page's "Explore top-rated categories" section. If left blank, it falls back to the default description.</small>
                                @error('top_rated_description')
                                    <div class="error text-danger">{{ \$message }}</div>
                                @enderror
                            </div>
                        </div>
HTML;

$replace = $search . "\n" . $faqAdminSection;

$content = str_replace($search, $replace, $content);
$search_n = str_replace("\r\n", "\n", $search);
$replace_n = str_replace("\r\n", "\n", $replace);
$content = str_replace($search_n, $replace_n, $content);

file_put_contents($filePath, $content);
echo "add.blade.php updated with FAQ section.\n";
