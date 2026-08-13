
<div class="nk-block nk-block-lg">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Business</h3>
            </div>

            {{-- 👇 Naya center block - Country/Region switcher --}}
            <div class="nk-block-head-content">
                <div class="form-group position-relative mb-0" style="min-width: 220px;">
                    <div class="position-relative">
                        <select
                            class="form-control @error('languages_supported') is-invalid @enderror pe-5"
                            wire:model.live="languages_supported">
                            <option value="">Select Region</option>
                            @foreach ($languages as $language)
                                <option value="{{ $language->id }}">{{ $language->name }}</option>
                            @endforeach
                        </select>
                        <i class="fa fa-chevron-down position-absolute"
                            style="right: 15px; top: 50%; transform: translateY(-50%); pointer-events: none;"></i>
                    </div>
                    @error('languages_supported')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            {{-- 👆 Naya block yahan tak --}}

            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    ...
                </div>
            </div>
        </div>
    </div>
    {{-- @if ($businessId) --}}
        <form wire:submit.prevent="{{ $businessId ? 'updateBusiness' : 'storeBusiness' }}" method="POST"
            enctype="multipart/form-data"
            x-data="{
                isAffiliate: @entangle('is_affiliate').live,
                editing: false,
                originalUrl: @entangle('affiliate_link').live,
                tempUrl: @entangle('affiliate_link').live,
                validationError: '',
                startEdit() {
                    this.editing = true;
                    this.originalUrl = this.tempUrl;
                    this.validationError = '';
                    this.$nextTick(() => this.$refs.urlInput && this.$refs.urlInput.focus());
                },
                saveEdit() {
                    this.validationError = '';
                    const urlPattern = /^(https?:\/\/)?(localhost(:\d+)?|[\da-z\.-]+\.[a-z\.]{2,6})(:[0-9]+)?(\/[\/\w \.-]*)*\/?(\?[;&a-z\d%_\.~+=-]*)?(#[a-z\d_-]*)?$/i;

                    if (this.tempUrl && this.tempUrl.trim() !== '' && !urlPattern.test(this.tempUrl)) {
                        this.validationError = 'Please enter a valid URL';
                        return;
                    }

                    this.editing = false;
                },
                cancelEdit() {
                    this.tempUrl = this.originalUrl;
                    this.validationError = '';
                    this.editing = false;
                }
            }">
            @if($is_affiliate == 0)
                <div x-show="!isAffiliate" x-cloak class="alert alert-warning py-2 px-3 mb-3 d-flex align-items-center"
                    role="alert"
                    style="font-size: 0.875rem; border-left: 4px solid #ffc107; background-color: #fffbeb;">
                    <em class="icon ni ni-alert-circle me-2 fs-5 text-warning"></em>
                    <span class="fw-bold text-dark">NO AFFILIATE URL ADDED</span>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-icon alert-dismissible mb-3" role="alert">
                    <em class="icon ni ni-cross-circle"></em>
                    <strong>Error:</strong> {{ session('error') }}
                    <button class="close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session()->has('success'))
                <div class="alert alert-success alert-icon alert-dismissible mb-3" role="alert">
                    <em class="icon ni ni-check-circle"></em>
                    <strong>Success:</strong> {{ session('success') }}
                    <button class="close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-3" role="alert">
                    <h6 class="alert-heading fw-bold mb-1"><em class="icon ni ni-alert-circle me-1"></em> Please fix the following errors:</h6>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-md-8">
                    {{-- =====================================================
                         AI OUTPUT UPLOAD PANEL
                         Paste full AI-generated text to auto-fill all fields
                    ====================================================== --}}
                    <div class="card card-bordered mb-3 border-primary"
                         x-data="{
                             open: false,
                             copyTemplate() {
                                 const t = '[name]\nBusiness Name Here\n\n[short_description]\nA short summary.\n\n[description_title]\nWhat is Business Name?\n\n[business_description]\n<p>Full HTML description...</p>\n\n[usps]\nFree domain & SSL certificate\n24/7 customer support\n99.9% uptime guarantee\nOne-click WordPress install\nFree CDN included\n\n[pros]\nExcellent customer support\nVery affordable pricing\nEasy-to-use control panel\n\n[cons]\nLimited basic plan storage\nRenewal prices increase\nNo free trial available\n\n[pro_cons_headline]\nBusiness Name Pros and Cons\n\n[pro_cons_intro]\n<p>Here is a quick summary of the pros and cons...</p>\n\n[pro_cons_summary]\n<p>Overall, a solid choice for beginners.</p>\n\n[offerings_headline]\nWhat Does Business Name Offer?\n\n[offerings_top_text]\n<p>Business Name offers a range of services...</p>\n\n[after_image_description]\n<p>In addition to the main product, they also offer...</p>\n\n[alternatives_title]\nBest Business Name Alternatives\n\n[alternatives_description]\n<p>Looking for alternatives?...</p>\n\n[alternatives_title_2]\nMore Alternatives\n\n[alternatives_description_2]\n<p>Additional alternatives content...</p>\n\n[reviews_title]\nBusiness Name Reviews & Ratings\n\n[reviews_description]\n<p>Customers rate Business Name highly for...</p>\n\n[reviews_title_2]\nMore User Reviews\n\n[reviews_description_2]\n<p>Additional review content...</p>\n\n[faqs_title]\nBusiness Name Frequently Asked Questions\n\n[faqs_description]\n<p>Here are the most common questions...</p>\n\n[faqs_title_2]\nMore FAQs\n\n[faqs_description_2]\n<p>Additional FAQ content...</p>\n\n[comparison_title]\nBusiness Name vs Competitors\n\n[comparison_description]\n<p>When comparing Business Name to others...</p>\n\n[comparison_title_2]\nDetailed Comparison\n\n[comparison_description_2]\n<p>A deep dive into how Business Name stacks up...</p>\n\n[meta_title]\nBusiness Name Review 2025 – Pricing, Features & More\n\n[meta_description]\nRead our in-depth Business Name review. Compare plans, pricing, and features.\n\n[alternatives_meta_title]\nBest Business Name Alternatives 2025\n\n[alternatives_meta_description]\nDiscover the best alternatives to Business Name. Compare features, pricing, and more.\n\n[reviews_meta_title]\nBusiness Name Reviews 2025 – Is It Worth It?\n\n[reviews_meta_description]\nRead real user reviews of Business Name. See ratings, pros, cons, and expert analysis.\n\n[faqs_meta_title]\nBusiness Name FAQ – Your Questions Answered\n\n[faqs_meta_description]\nFind answers to the most common questions about Business Name, pricing, features, and support.\n\n[comparison_meta_title]\nBusiness Name vs Competitors – Detailed Comparison\n\n[comparison_meta_description]\nCompare Business Name side-by-side with top competitors. Features, pricing, and verdict.';
                                 navigator.clipboard.writeText(t).then(function() {
                                     alert('Format template copied to clipboard! Paste it in your AI chat and ask it to fill in the content, then paste the result back here.');
                                 }).catch(function() {
                                     prompt('Copy this format template:', t);
                                 });
                             }
                         }">
                        {{-- Card Header / Toggle --}}
                        <div class="card-header d-flex align-items-center justify-content-between py-2 px-3"
                             style="cursor:pointer; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 4px 4px 0 0;"
                             @click="open = !open">
                            <div class="d-flex align-items-center gap-2">
                                <span style="font-size:1.1rem;">&#129302;</span>
                                <span class="fw-semibold text-white" style="font-size:0.95rem; letter-spacing:0.01em;">
                                    AI Output Upload
                                </span>
                                <span class="badge bg-white text-primary ms-1" style="font-size:0.72rem;">
                                    Auto-fill all fields
                                </span>
                            </div>
                            <em class="icon ni text-white" :class="open ? 'ni-chevron-up' : 'ni-chevron-down'"></em>
                        </div>

                        {{-- Collapsible Body --}}
                        <div x-show="open" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100">
                            <div class="card-inner">
                                {{-- Instructions --}}
                                <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-start " style="font-size:0.82rem;">
                                    <em class="icon ni ni-info me-1 mt-1 flex-shrink-0"></em>
                                    <div>
                                        <strong>How to use:</strong>
                                        Click <em>Copy Format Template</em>, paste it into your AI chat, and ask it to fill in the content for this business.
                                        Then paste the AI response below and click <strong>Apply Content</strong> &mdash; all fields will be auto-filled.
                                    </div>
                                </div>

                                {{-- Copy Format Button --}}
                                <div class="mb-3">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" @click="copyTemplate()">
                                        <em class="icon ni ni-copy me-1"></em> Copy Format Template
                                    </button>
                                    <span class="text-muted ms-2" style="font-size:0.78rem;">
                                        Copies the required AI output format to your clipboard
                                    </span>
                                </div>

                                {{-- Textarea --}}
                                <div class="form-group mb-3">
                                    <label class="form-label fw-semibold mb-1">
                                        Paste AI-generated content below:
                                    </label>
                                    <textarea
                                        wire:model="aiOutputText"
                                        class="form-control font-monospace"
                                        rows="18"
                                        placeholder="[name]&#10;Business Name Here&#10;&#10;[short_description]&#10;A short description...&#10;&#10;[business_description]&#10;<p>Full description HTML...</p>&#10;&#10;[usps]&#10;USP line 1&#10;USP line 2&#10;&#10;... etc"
                                        style="resize:vertical; font-size:0.82rem; line-height:1.5; border: 2px solid #dee2e6; border-radius:6px;"
                                    ></textarea>
                                </div>

                                {{-- Status Feedback --}}
                                @if($aiApplyStatus === 'success')
                                    <div class="alert alert-success py-2 px-3 mb-3 d-flex align-items-center gap-2" style="font-size:0.84rem;">
                                        <em class="icon ni ni-check-circle-fill text-success"></em>
                                        {{ $aiApplyMessage }}
                                    </div>
                                @elseif($aiApplyStatus === 'error')
                                    <div class="alert alert-danger py-2 px-3 mb-3 d-flex align-items-center gap-2" style="font-size:0.84rem;">
                                        <em class="icon ni ni-alert-circle text-danger"></em>
                                        {{ $aiApplyMessage }}
                                    </div>
                                @endif

                                {{-- Action Buttons --}}
                                <div class="d-flex gap-2 align-items-center">
                                    <button
                                        type="button"
                                        wire:click="parseAndApplyAiOutput"
                                        wire:loading.attr="disabled"
                                        class="btn btn-primary"
                                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                        <span wire:loading.remove wire:target="parseAndApplyAiOutput">
                                            <em class="icon ni ni-spark me-1"></em> Apply Content
                                        </span>
                                        <span wire:loading wire:target="parseAndApplyAiOutput">
                                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                            Applying...
                                        </span>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                            wire:click="$set('aiOutputText', '')"
                                            wire:click.prevent
                                            title="Clear the textarea">
                                        <em class="icon ni ni-trash"></em> Clear
                                    </button>
                                    <span class="text-muted ms-auto" style="font-size:0.76rem;">
                                        Only filled sections will overwrite existing field values.
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- END AI Output Upload Panel --}}

                    <!-- Business Name Section -->

                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="form-group d-flex justify-content-between align-items-center">
                                <label class="form-label">Business Name</label>

                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    wire:model.live="name" />
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Preview Description (short_description) --}}
                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="form-group d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0">Preview Description</label>
                                <span class="text-muted" style="font-size:0.78rem;">Shown on category, top-rated &amp; most-popular pages</span>
                            </div>
                            <div class="form-group">
                                <textarea
                                    class="form-control @error('short_description') is-invalid @enderror"
                                    wire:model.live="short_description"
                                    rows="3"
                                    placeholder="A short summary shown in business listing cards..."
                                    style="resize:vertical;"
                                ></textarea>
                                @error('short_description')
                                    <div class="text-danger mt-1" style="font-size:0.82rem;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- USP / Key Selling Points Section --}}
                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="form-group d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <label class="form-label mb-0"><strong>Key Selling Points (USPs)</strong></label>
                                    <p class="text-muted small mb-0">Up to 6 short bullet points shown with ✓ on the product page.</p>
                                </div>
                            </div>

                            @foreach ($businessUsps as $index => $usp)
                                <div class="d-flex align-items-center mb-2 gap-2">
                                    <span class="text-success fw-bold me-1" style="font-size:18px;">✓</span>
                                    <input
                                        type="text"
                                        class="form-control"
                                        wire:model.live="businessUsps.{{ $index }}.text"
                                        placeholder="e.g. Free domain & SSL certificate"
                                        maxlength="255"
                                    />
                                    <button type="button" class="btn btn-sm btn-danger ms-1" wire:click="removeUsp({{ $index }})" title="Remove">
                                        <em class="icon ni ni-trash"></em>
                                    </button>
                                </div>
                            @endforeach

                            @if (count($businessUsps) < 6)
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" wire:click="addUsp">
                                    <em class="icon ni ni-plus"></em> Add USP
                                </button>
                            @else
                                <p class="text-muted small mt-2 mb-0">Maximum of 6 USPs reached.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Business Description Section -->
                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="form-group mb-3">
                                <label class="form-label">Business description title</label>
                                <input type="text" class="form-control @error('description_title') is-invalid @enderror"
                                    wire:model.live="description_title" placeholder="e.g. What is {{ $name ?: 'Business Name' }}" />
                                @error('description_title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group d-flex justify-content-between align-items-center">
                                <label class="form-label">Business description</label>
                                {{-- <button type="button" class="btn btn-sm btn-secondary"
                                wire:click="setFieldIdAndOpenModal('description', 1000, {{ $businessId ?? 'null' }})">
                                    AI Autofill
                                </button> --}}
                            </div>

                            <!-- If you're using Alpine.js -->
                            <div wire:ignore x-data="{
                                editor: null,
                                init() {
                                    this.$nextTick(() => {
                                        ClassicEditor
                                            .create(this.$refs.editor)
                                            .then(editor => {
                                                this.editor = editor;
                                                editor.model.document.on('change:data', () => {
                                                    this.$wire.business_description = editor.getData();
                                                });
                                                // AI Output Upload listener
                                                window.addEventListener('ai-content-applied', function(e) {
                                                    if (e.detail && e.detail.fields && e.detail.fields.business_description !== undefined) {
                                                        editor.setData(e.detail.fields.business_description);
                                                    }
                                                });
                                            })
                                            .catch(error => {
                                                console.error(error);
                                            });
                                    });
                                }
                            }">
                                <div class="form-group">
                                    <textarea
                                        x-ref="editor"
                                        class="form-control b_description @error('business_description') is-invalid @enderror"
                                        wire:model.live="business_description"
                                        rows="5">
                                    </textarea>
                                    @error('business_description')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                <div class="card card-bordered mb-3">

                    <div class="card-inner">
                        <div class="col-12 mb-4">
                            <div class="form-group">
                                <label class="form-label">Pros & cons headline</label>
                                <input type="text"
                                    class="form-control"
                                    wire:model.live="pro_cons_headline"
                                    placeholder="Enter headline...">
                            </div>
                        </div>

                        <div class="row">

                            <!-- Introduction -->
                            <div class="col-12 mb-4">
                                <div class="form-group">
                                    <label class="form-label">Pros &amp; cons introduction</label>
                                    <div wire:ignore x-data="{
                                        editor: null,
                                        init() {
                                            this.$nextTick(() => {
                                                ClassicEditor
                                                    .create(this.$refs.editor_pro_cons_intro)
                                                    .then(editor => {
                                                        this.editor = editor;
                                                        editor.model.document.on('change:data', () => {
                                                            this.$wire.set('pro_cons_intro', editor.getData());
                                                        });
                                                        // AI Output Upload listener
                                                        window.addEventListener('ai-content-applied', function(e) {
                                                            if (e.detail && e.detail.fields && e.detail.fields.pro_cons_intro !== undefined) {
                                                                editor.setData(e.detail.fields.pro_cons_intro);
                                                            }
                                                        });
                                                    })
                                                    .catch(error => {
                                                        console.error(error);
                                                    });
                                            });
                                        }
                                    }">
                                        <textarea
                                            x-ref="editor_pro_cons_intro"
                                            class="form-control"
                                            wire:model.live="pro_cons_intro"
                                            rows="3"
                                            placeholder="Introductory text shown above the Pros and Cons boxes..."></textarea>
                                    </div>
                                </div>
                            </div>



                            <!-- Summary -->
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label class="form-label">Pros &amp; cons ending text</label>
                                    <div wire:ignore x-data="{
                                        editor: null,
                                        init() {
                                            this.$nextTick(() => {
                                                ClassicEditor
                                                    .create(this.$refs.editor_pro_cons_summary)
                                                    .then(editor => {
                                                        this.editor = editor;
                                                        editor.model.document.on('change:data', () => {
                                                            this.$wire.set('pro_cons_summary', editor.getData());
                                                        });
                                                        // AI Output Upload listener
                                                        window.addEventListener('ai-content-applied', function(e) {
                                                            if (e.detail && e.detail.fields && e.detail.fields.pro_cons_summary !== undefined) {
                                                                editor.setData(e.detail.fields.pro_cons_summary);
                                                            }
                                                        });
                                                    })
                                                    .catch(error => {
                                                        console.error(error);
                                                    });
                                            });
                                        }
                                    }">
                                        <textarea
                                            x-ref="editor_pro_cons_summary"
                                            class="form-control"
                                            wire:model.live="pro_cons_summary"
                                            rows="3"
                                            placeholder="Summary text shown below the Pros and Cons boxes..."></textarea>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <label class="form-label mb-0"><strong>Description of products/services</strong></label>
                                    <p class="text-muted small mb-0">Summarize offered products with a headline, images, and text.</p>
                                </div>
                            </div>
                            
                            <div class="border p-3 mb-3 rounded">
                                <div class="form-group mb-3 mt-3">
                                    <label class="form-label">Products/services headline</label>
                                    <input type="text" class="form-control" wire:model.live="businessOfferings.0.headline" placeholder="e.g. What does Bluehost offer?">
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="form-label">Products/services introduction</label>
                                    <div wire:ignore x-data="{
                                        editor: null,
                                        init() {
                                            this.$nextTick(() => {
                                                ClassicEditor
                                                    .create(this.$refs.editor_offerings_top_text)
                                                    .then(editor => {
                                                        this.editor = editor;
                                                        editor.model.document.on('change:data', () => {
                                                            this.$wire.set('businessOfferings.0.top_text', editor.getData());
                                                        });
                                                        // AI Output Upload listener
                                                        window.addEventListener('ai-content-applied', function(e) {
                                                            if (e.detail && e.detail.fields && e.detail.fields.offerings_top_text !== undefined) {
                                                                editor.setData(e.detail.fields.offerings_top_text);
                                                            }
                                                        });
                                                    })
                                                    .catch(error => {
                                                        console.error(error);
                                                    });
                                            });
                                        }
                                    }">
                                        <textarea
                                            x-ref="editor_offerings_top_text"
                                            class="form-control"
                                            wire:model.live="businessOfferings.0.top_text"
                                            rows="3"
                                            placeholder="Introduction text above the image..."></textarea>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="form-label">Products/services ending text</label>
                                    <div wire:ignore x-data="{
                                        editor: null,
                                        init() {
                                            this.$nextTick(() => {
                                                ClassicEditor
                                                    .create(this.$refs.afterImageEditor)
                                                    .then(editor => {
                                                        this.editor = editor;
                                                        editor.model.document.on('change:data', () => {
                                                            this.$wire.after_image_description = editor.getData();
                                                        });
                                                        // AI Output Upload listener
                                                        window.addEventListener('ai-content-applied', function(e) {
                                                            if (e.detail && e.detail.fields && e.detail.fields.after_image_description !== undefined) {
                                                                editor.setData(e.detail.fields.after_image_description);
                                                            }
                                                        });
                                                    })
                                                    .catch(error => {
                                                        console.error(error);
                                                    });
                                            });
                                        }
                                    }">
                                        <textarea
                                            x-ref="afterImageEditor"
                                            class="form-control b_description @error('after_image_description') is-invalid @enderror"
                                            wire:model.live="after_image_description"
                                            rows="5">
                                        </textarea>
                                        @error('after_image_description')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alternatives Page Description Section -->
                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="form-group mb-3">
                                <label class="form-label">Alternatives page title</label>
                                <input type="text" class="form-control @error('alternatives_title') is-invalid @enderror"
                                    wire:model.live="alternatives_title" placeholder="e.g. Best {{ $name ?: 'Business Name' }} Alternatives" />
                                @error('alternatives_title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group d-flex justify-content-between align-items-center">
                                <label class="form-label">Alternatives page description</label>
                            </div>

                            <div wire:ignore x-data="{
                                editor: null,
                                init() {
                                    this.$nextTick(() => {
                                        ClassicEditor
                                            .create(this.$refs.editor)
                                            .then(editor => {
                                                this.editor = editor;
                                                editor.model.document.on('change:data', () => {
                                                    this.$wire.alternatives_description = editor.getData();
                                                });
                                                // AI Output Upload listener
                                                window.addEventListener('ai-content-applied', function(e) {
                                                    if (e.detail && e.detail.fields && e.detail.fields.alternatives_description !== undefined) {
                                                        editor.setData(e.detail.fields.alternatives_description);
                                                    }
                                                });
                                            })
                                            .catch(error => {
                                                console.error(error);
                                            });
                                    });
                                }
                            }">
                                <div class="form-group">
                                    <textarea
                                        x-ref="editor"
                                        class="form-control @error('alternatives_description') is-invalid @enderror"
                                        wire:model.live="alternatives_description"
                                        rows="5">
                                    </textarea>
                                    @error('alternatives_description')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reviews Page Description Section -->
                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="form-group mb-3">
                                <label class="form-label">Reviews page title</label>
                                <input type="text" class="form-control @error('reviews_title') is-invalid @enderror"
                                    wire:model.live="reviews_title" placeholder="e.g. {{ $name ?: 'Business Name' }} Reviews & Ratings" />
                                @error('reviews_title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group d-flex justify-content-between align-items-center">
                                <label class="form-label">Reviews page description</label>
                            </div>

                            <div wire:ignore x-data="{
                                editor: null,
                                init() {
                                    this.$nextTick(() => {
                                        ClassicEditor
                                            .create(this.$refs.editor)
                                            .then(editor => {
                                                this.editor = editor;
                                                editor.model.document.on('change:data', () => {
                                                    this.$wire.reviews_description = editor.getData();
                                                });
                                                // AI Output Upload listener
                                                window.addEventListener('ai-content-applied', function(e) {
                                                    if (e.detail && e.detail.fields && e.detail.fields.reviews_description !== undefined) {
                                                        editor.setData(e.detail.fields.reviews_description);
                                                    }
                                                });
                                            })
                                            .catch(error => {
                                                console.error(error);
                                            });
                                    });
                                }
                            }">
                                <div class="form-group">
                                    <textarea
                                        x-ref="editor"
                                        class="form-control @error('reviews_description') is-invalid @enderror"
                                        wire:model.live="reviews_description"
                                        rows="5">
                                    </textarea>
                                    @error('reviews_description')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr class="preview-hr my-4">

                            <div class="form-group mb-3">
                                <label class="form-label">Reviews page title 2</label>
                                <input type="text" class="form-control @error('reviews_title_2') is-invalid @enderror"
                                    wire:model.live="reviews_title_2" placeholder="e.g. {{ $name ?: 'Business Name' }} Additional Reviews Info" />
                                @error('reviews_title_2')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group d-flex justify-content-between align-items-center">
                                <label class="form-label">Reviews page description 2</label>
                            </div>

                            <div wire:ignore x-data="{
                                editor: null,
                                init() {
                                    this.$nextTick(() => {
                                        ClassicEditor
                                            .create(this.$refs.editor)
                                            .then(editor => {
                                                this.editor = editor;
                                                editor.model.document.on('change:data', () => {
                                                    this.$wire.reviews_description_2 = editor.getData();
                                                });
                                                // AI Output Upload listener
                                                window.addEventListener('ai-content-applied', function(e) {
                                                    if (e.detail && e.detail.fields && e.detail.fields.reviews_description_2 !== undefined) {
                                                        editor.setData(e.detail.fields.reviews_description_2);
                                                    }
                                                });
                                            })
                                            .catch(error => {
                                                console.error(error);
                                            });
                                    });
                                }
                            }">
                                <div class="form-group">
                                    <textarea
                                        x-ref="editor"
                                        class="form-control @error('reviews_description_2') is-invalid @enderror"
                                        wire:model.live="reviews_description_2"
                                        rows="5">
                                    </textarea>
                                    @error('reviews_description_2')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQs Page Description Section -->
                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="form-group mb-3">
                                <label class="form-label">FAQs page title 1</label>
                                <input type="text" class="form-control @error('faqs_title') is-invalid @enderror"
                                    wire:model.live="faqs_title" placeholder="e.g. {{ $name ?: 'Business Name' }} Frequently Asked Questions" />
                                @error('faqs_title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">FAQs page description 1</label>
                                <div wire:ignore x-data="{
                                    editor: null,
                                    init() {
                                        this.$nextTick(() => {
                                            ClassicEditor
                                                .create(this.$refs.editor)
                                                .then(editor => {
                                                    this.editor = editor;
                                                    editor.model.document.on('change:data', () => {
                                                        this.$wire.faqs_description = editor.getData();
                                                    });
                                                    // AI Output Upload listener
                                                    window.addEventListener('ai-content-applied', function(e) {
                                                        if (e.detail && e.detail.fields && e.detail.fields.faqs_description !== undefined) {
                                                            editor.setData(e.detail.fields.faqs_description);
                                                        }
                                                    });
                                                })
                                                .catch(error => {
                                                    console.error(error);
                                                });
                                        });
                                    }
                                }">
                                    <textarea
                                        x-ref="editor"
                                        class="form-control @error('faqs_description') is-invalid @enderror"
                                        wire:model.live="faqs_description"
                                        rows="5">
                                    </textarea>
                                    @error('faqs_description')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">FAQs page title 2</label>
                                <input type="text" class="form-control @error('faqs_title_2') is-invalid @enderror"
                                    wire:model.live="faqs_title_2" placeholder="e.g. Additional Questions for {{ $name ?: 'Business Name' }}" />
                                @error('faqs_title_2')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">FAQs page description 2</label>
                                <div wire:ignore x-data="{
                                    editor: null,
                                    init() {
                                        this.$nextTick(() => {
                                            ClassicEditor
                                                .create(this.$refs.editor2)
                                                .then(editor => {
                                                    this.editor = editor;
                                                    editor.model.document.on('change:data', () => {
                                                        this.$wire.faqs_description_2 = editor.getData();
                                                    });
                                                    // AI Output Upload listener
                                                    window.addEventListener('ai-content-applied', function(e) {
                                                        if (e.detail && e.detail.fields && e.detail.fields.faqs_description_2 !== undefined) {
                                                            editor.setData(e.detail.fields.faqs_description_2);
                                                        }
                                                    });
                                                })
                                                .catch(error => {
                                                    console.error(error);
                                                });
                                        });
                                    }
                                }">
                                    <textarea
                                        x-ref="editor2"
                                        class="form-control @error('faqs_description_2') is-invalid @enderror"
                                        wire:model.live="faqs_description_2"
                                        rows="5">
                                    </textarea>
                                    @error('faqs_description_2')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comparison Page Description Section -->
                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="form-group mb-3">
                                <label class="form-label">Comparison page title 1</label>
                                <input type="text" class="form-control @error('comparison_title') is-invalid @enderror"
                                    wire:model.live="comparison_title" placeholder="e.g. {{ $name ?: 'Business Name' }} Comparison" />
                                @error('comparison_title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Comparison page description 1</label>
                                <div wire:ignore x-data="{
                                    editor: null,
                                    init() {
                                        this.$nextTick(() => {
                                            ClassicEditor
                                                .create(this.$refs.editor)
                                                .then(editor => {
                                                    this.editor = editor;
                                                    editor.model.document.on('change:data', () => {
                                                        this.$wire.comparison_description = editor.getData();
                                                    });
                                                    // AI Output Upload listener
                                                    window.addEventListener('ai-content-applied', function(e) {
                                                        if (e.detail && e.detail.fields && e.detail.fields.comparison_description !== undefined) {
                                                            editor.setData(e.detail.fields.comparison_description);
                                                        }
                                                    });
                                                })
                                                .catch(error => {
                                                    console.error(error);
                                                });
                                        });
                                    }
                                }">
                                    <textarea
                                        x-ref="editor"
                                        class="form-control @error('comparison_description') is-invalid @enderror"
                                        wire:model.live="comparison_description"
                                        rows="5">
                                    </textarea>
                                    @error('comparison_description')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Comparison page title 2</label>
                                <input type="text" class="form-control @error('comparison_title_2') is-invalid @enderror"
                                    wire:model.live="comparison_title_2" placeholder="e.g. Detailed comparison for {{ $name ?: 'Business Name' }}" />
                                @error('comparison_title_2')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Comparison page description 2</label>
                                <div wire:ignore x-data="{
                                    editor: null,
                                    init() {
                                        this.$nextTick(() => {
                                            ClassicEditor
                                                .create(this.$refs.editor2)
                                                .then(editor => {
                                                    this.editor = editor;
                                                    editor.model.document.on('change:data', () => {
                                                        this.$wire.comparison_description_2 = editor.getData();
                                                    });
                                                    // AI Output Upload listener
                                                    window.addEventListener('ai-content-applied', function(e) {
                                                        if (e.detail && e.detail.fields && e.detail.fields.comparison_description_2 !== undefined) {
                                                            editor.setData(e.detail.fields.comparison_description_2);
                                                        }
                                                    });
                                                })
                                                .catch(error => {
                                                    console.error(error);
                                                });
                                        });
                                    }
                                }">
                                    <textarea
                                        x-ref="editor2"
                                        class="form-control @error('comparison_description_2') is-invalid @enderror"
                                        wire:model.live="comparison_description_2"
                                        rows="5">
                                    </textarea>
                                    @error('comparison_description_2')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between mb-3">
                                        <a href="#" class="btn btn-link text-center"><span><b>View
                                                    Page</b></span></a>
                                        <button type="submit" class="btn btn-primary btn-localio" wire:loading.attr="disabled">
                                            <span wire:loading.remove>{{ $businessId ? 'Update' : 'Save' }}</span>
                                            <span wire:loading><span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...</span>
                                        </button>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label d-block text-left">Published</label>
                                        <div class="d-flex align-items-center justify-content-left">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input"
                                                    id="businessStatusSwitch" name="status" wire:model="status"
                                                    value="1" {{ $status == 1 ? 'checked' : '' }}>
                                                <label class="custom-control-label"
                                                    for="businessStatusSwitch"></label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                     @php
                                        $langId = session('lang_id', 1);

                                        $subCategories = \App\Models\Category::whereNotNull('parent_id')
                                            ->whereHas('translations', function ($query) use ($langId) {
                                                $query->where('lang_id', $langId);
                                            })
                                            ->with(['translations' => function ($query) use ($langId) {
                                                $query->where('lang_id', $langId);
                                            }])
                                            ->get();
                                    @endphp
                                    <hr class="my-3">
                                    <div class="form-group mt-3">
                                        <label class="form-label" for="admin_rating">Review Rating (Admin)</label>
                                        <input type="number" step="0.1" min="1" max="5" id="admin_rating" wire:model.defer="admin_rating" class="form-control" placeholder="e.g. 4.5">
                                        <small class="form-text text-muted">Set initial overall review rating for this business (1.0 to 5.0 stars). Used until the business gets approved user reviews.</small>
                                        @error('admin_rating')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group mt-3" x-data="{
                                        search: '',
                                        selectCategory(id) {
                                            $wire.set('selected_category', id);
                                        }
                                    }">
                                        <label class="form-label">Primary sub-category</label>

                                        <!-- Search input for primary sub-category -->
                                        <div class="mb-2">
                                            <input type="text" x-model="search" class="form-control" placeholder="Search primary sub-category...">
                                        </div>

                                        <!-- Scrollable list of radio options -->
                                        <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                            <div class="form-check py-1 border-bottom">
                                                <input type="radio" id="primary_cat_none" name="primary_sub_category" class="form-check-input"
                                                    value="" :checked="!$wire.selected_category" @change="selectCategory('')">
                                                <label class="form-check-label w-100 text-muted" for="primary_cat_none">Select Category</label>
                                            </div>
                                            @if (isset($categories))
                                                @foreach ($subCategories as $category)
                                                    @php
                                                        $catName = $category->translations->name ?? $category->categoryTranslations->first()->name ?? 'Category #' . $category->id;
                                                    @endphp
                                                    <div class="form-check py-1 border-bottom"
                                                         x-show="'{{ strtolower(addslashes($catName)) }}'.includes(search.toLowerCase())">
                                                        <input type="radio"
                                                            id="primary_cat_{{ $category->id }}"
                                                            name="primary_sub_category"
                                                            class="form-check-input"
                                                            value="{{ $category->id }}"
                                                            :checked="$wire.selected_category == {{ $category->id }}"
                                                            @change="selectCategory({{ $category->id }})">
                                                        <label class="form-check-label w-100" for="primary_cat_{{ $category->id }}">
                                                            {{ $catName }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>

                                        @error('selected_category')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    
                                    {{-- All sub-category --}}

                                    <div class="form-group mt-3" x-data="{
                                        search: '',
                                        toggleCategory(id) {
                                            let selected = [...$wire.selected_sub_categories];
                                            let index = selected.indexOf(id);
                                            if (index > -1) {
                                                selected.splice(index, 1);
                                            } else {
                                                selected.push(id);
                                            }
                                            $wire.set('selected_sub_categories', selected);
                                        },
                                        selectAll(ids) {
                                            $wire.set('selected_sub_categories', ids);
                                        },
                                        clearAll() {
                                            $wire.set('selected_sub_categories', []);
                                        }
                                    }">
                                        <label class="form-label">Secondary sub-categories</label>
                                        
                                        @php
                                            $allSubCatIds = $subCategories->pluck('id')->toArray();
                                            $subCatMap = [];
                                            foreach ($subCategories as $cat) {
                                                $subCatMap[$cat->id] = $cat->translations->name ?? $cat->categoryTranslations->first()->name ?? 'Category #' . $cat->id;
                                            }
                                        @endphp

                                        <!-- Search & Bulk Control Buttons -->
                                        <div class="row mb-2">
                                            <div class="col-md-12">
                                                <div class="input-group">
                                                    <input type="text" x-model="search" class="form-control" placeholder="Search sub-categories...">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-primary btn-sm d-none" @click="selectAll({{ json_encode($allSubCatIds) }})" type="button">
                                                            Select All
                                                        </button>
                                                        <button class="btn btn-outline-danger btn-sm d-none" @click="clearAll()" type="button">
                                                            Clear All
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Scrollable List of Checkboxes -->
                                        <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto; ">
                                            @if($subCategories->isEmpty())
                                                <p class="text-center text-muted my-2">No sub-categories found</p>
                                            @else
                                                @foreach($subCategories as $category)
                                                    @php
                                                        $catName = $category->translations->name ?? $category->categoryTranslations->first()->name ?? 'Category #' . $category->id;
                                                    @endphp
                                                    <div class="form-check py-1 border-bottom"
                                                         x-show="'{{ strtolower(addslashes($catName)) }}'.includes(search.toLowerCase())">
                                                        <input type="checkbox"
                                                            id="subcat_{{ $category->id }}"
                                                            class="form-check-input"
                                                            :checked="$wire.selected_sub_categories.includes({{ $category->id }})"
                                                            @change="toggleCategory({{ $category->id }})">
                                                        <label class="form-check-label w-100" for="subcat_{{ $category->id }}">
                                                            {{ $catName }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>

                                        <!-- Selected Sub-Categories Display Badges -->
                                        <div class="mt-2">
                                            <label class="form-label">Selected Sub-categories:
                                                <span class="badge bg-primary" x-text="$wire.selected_sub_categories.length"></span>
                                            </label>
                                            <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto; height: 150px;">
                                                <template x-if="$wire.selected_sub_categories && $wire.selected_sub_categories.length > 0">
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @php
                                                            $subCatJsMap = json_encode($subCatMap);
                                                        @endphp
                                                        <template x-for="catId in $wire.selected_sub_categories" :key="catId">
                                                            <span class="badge bg-primary position-relative m-1" style="padding: 5px 20px 5px 8px; font-size: 0.75rem;">
                                                                <span x-text="({{ $subCatJsMap }})[catId] || ('Category #' + catId)"></span>
                                                                <button type="button"
                                                                    @click="toggleCategory(catId)"
                                                                    class="btn-close btn-close-white position-absolute"
                                                                    style="top: 50%; right: 4px; transform: translateY(-50%); font-size: 0.5rem;"
                                                                    aria-label="Remove"></button>
                                                            </span>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template x-if="!$wire.selected_sub_categories || $wire.selected_sub_categories.length === 0">
                                                    <p class="text-center text-muted my-1 mb-0 small">No sub-categories selected</p>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="my-3">
                                    <div class="form-group mt-3">
                                        <label class="form-label fw-semibold" for="business-slug">
                                            Permanent Link
                                            <span class="text-muted fw-normal ms-1" style="font-size:0.8rem;">(URL slug)</span>
                                        </label>
                                        <div class="input-group @error('slug') has-error @enderror" style="border-radius:8px; overflow:hidden; border:1.5px solid #dee2e6; background:#fff;">
                                            <span class="input-group-text" style="background:#f0f3f7; border:none; border-right:1.5px solid #dee2e6; color:#6c757d; font-size:0.85rem; padding:0 12px; white-space:nowrap; font-weight:500;">localio.com/</span>
                                            <input
                                                type="text"
                                                id="business-slug"
                                                wire:model.live="slug"
                                                class="form-control"
                                                style="border:none; background:#fff; font-size:0.9rem; box-shadow:none; padding:10px 14px;"
                                                placeholder="your-business-name"
                                                autocomplete="off"
                                                spellcheck="false"
                                            >
                                        </div>
                                        @error('slug')
                                            <div class="text-danger mt-1" style="font-size:0.82rem;"><em class="icon ni ni-alert-circle me-1"></em>{{ $message }}</div>
                                        @enderror
                                        <div class="text-muted mt-1" style="font-size:0.78rem;">Only letters, numbers, hyphens and underscores. This sets the public business URL.</div>
                                    </div>
                                    <hr class="my-3">
                                        <div class="form-group mt-3">
                                            <label class="form-label">Active Countries/Regions</label>
                                            <div class="d-flex mb-2">
                                                <div class="form-check me-3">
                                                    <input class="form-check-input" type="radio" id="all_countries"
                                                        value="1" wire:model="active_all_countries"
                                                        wire:change.live="toggleAllCountries">
                                                    <label class="form-check-label" for="all_countries">All</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        id="specific_countries" value="0"
                                                        wire:model="active_all_countries"
                                                        wire:change.live="toggleAllCountries">
                                                    <label class="form-check-label" for="specific_countries">Specific
                                                        Countries/Regions</label>
                                                </div>
                                            </div>
                                            @if ($active_all_countries == 0)
                                                <!-- Country Selection Controls -->
                                                <div class="row mb-2">
                                                    <div class="col-md-12">
                                                        <div class="input-group">
                                                            <input type="text" wire:model.live="countrySearch"
                                                                class="form-control" placeholder="Search countries...">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-primary btn-sm"
                                                                    wire:click="selectAllCountries" type="button">
                                                                    Select All
                                                                </button>
                                                                <button class="btn btn-outline-danger btn-sm"
                                                                    wire:click="clearAllCountries" type="button">
                                                                    Clear All
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Countries Selection Area -->
                                                <div class="border rounded p-2"
                                                    style="max-height: 200px; overflow-y: auto;">
                                                    @if (empty($countries))
                                                        <p class="text-center text-muted my-2">No countries found</p>
                                                    @else
                                                        @foreach ($countries as $country)
                                                            @php
                                                                $rawName = $country->name;
                                                                // Normalize dashes (hyphen, en-dash, em-dash) and split
                                                                $normalized = preg_replace('/[\x{2013}\x{2014}-]+/u', '|', $rawName);
                                                                $parts = array_unique(array_filter(array_map('trim', explode('|', $normalized))));
                                                                $cleanCountryName = !empty($parts) ? reset($parts) : $rawName;
                                                                $langName = $country->language ? $country->language->name : '';
                                                                $displayName = $cleanCountryName . ($langName ? ' - ' . $langName : '');
                                                            @endphp
                                                            <div class="form-check py-1 border-bottom">
                                                                <input type="checkbox"
                                                                    wire:click="toggleCountrySelection({{ $country->id }})"
                                                                    id="country_{{ $country->id }}"
                                                                    class="form-check-input"
                                                                    {{ in_array($country->id, $selectedCountries) ? 'checked' : '' }}>
                                                                <label class="form-check-label w-100"
                                                                     for="country_{{ $country->id }}">
                                                                    <!-- {{ $displayName }} -->
                                                                    {{ $langName ?? $cleanCountryName ?? '' }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <!-- Selected Countries Display -->
                                                <div class="mt-2">
                                                    <label class="form-label">Selected Countries:
                                                        <span
                                                            class="badge bg-primary">{{ count($selectedCountries) }}</span>
                                                    </label>
                                                    <div class="border rounded p-2"
                                                        style="max-height:150px; overflow-y: auto;">
                                                        @if (!empty($selectedCountries) && count($selectedCountries) > 0)
                                                            <div class="d-flex flex-wrap gap-2" style="height:150px;">
                                                                @foreach ($selectedCountries as $countryId)
                                                                    @php
                                                                        $selectedCountry = collect($countries)->firstWhere(
                                                                            'id',
                                                                            $countryId,
                                                                        );
                                                                        $selectedDisplayName = '';
                                                                        if ($selectedCountry) {
                                                                            $rawName = $selectedCountry->name;
                                                                            $normalized = preg_replace('/[\x{2013}\x{2014}-]+/u', '|', $rawName);
                                                                            $parts = array_unique(array_filter(array_map('trim', explode('|', $normalized))));
                                                                            $cleanCountryName = !empty($parts) ? reset($parts) : $rawName;
                                                                            $langName = $selectedCountry->language ? $selectedCountry->language->name : '';
                                                                            $selectedDisplayName = $cleanCountryName . ($langName ? ' - ' . $langName : '');
                                                                        }
                                                                    @endphp
                                                                    @if ($selectedCountry)
                                                                        <span
                                                                            class="badge bg-primary position-relative m-1"
                                                                            style="padding: 5px 20px 5px 8px; font-size: 0.75rem;">
                                                                            <!-- {{ $selectedDisplayName }} -->
                                                                            {{ $langName ?? $cleanCountryName ?? '' }}
                                                                            <button type="button"
                                                                                wire:click="toggleCountrySelection({{ $countryId }})"
                                                                                class="btn-close btn-close-white position-absolute"
                                                                                style="top: 50%; right: 4px; transform: translateY(-50%); font-size: 0.5rem;"
                                                                                title="Remove {{ $langName ?? $cleanCountryName ?? '' }}">
                                                                            </button>
                                                                        </span>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <p class="text-center text-muted my-2">No countries selected
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                                @error('selectedCountries')
                                                    <div class="text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            @endif
                                        </div>

                                </div>
                            </div>
                        </div>
                    </div>


                    

                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <!-- Headline & Add URL Button -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label m-0 fs-6 fw-bold">Redirect URL</label>
                                <div x-show="isAffiliate">
                                    @if (!empty($selectedCountries))
                                        <button type="button" class="btn btn-sm text-white"
                                            wire:click="showAddUrlForm" style="background-color: #F9633B; border-color: #F9633B;">
                                            <em class="icon ni ni-plus me-1"></em>Add URL
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Affiliate Toggle directly below headline -->
                            <div class="p-2 px-3 mb-3 bg-light rounded-2 border d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="fw-bold text-dark">Affiliate</span>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            x-model="isAffiliate" id="affiliateToggleMain" style="cursor: pointer; width: 2.75rem; height: 1.35rem;">
                                    </div>
                                </div>
                                <div>
                                    <template x-if="isAffiliate">
                                        <span class="badge badge-dim bg-outline-success d-inline-flex align-items-center px-2 py-1">
                                            <em class="icon ni ni-check-circle me-1 fs-6"></em> Active
                                        </span>
                                    </template>
                                    <template x-if="!isAffiliate">
                                        <span class="badge badge-dim bg-outline-warning d-inline-flex align-items-center px-2 py-1">
                                            <em class="icon ni ni-alert-circle me-1 fs-6"></em> Disabled
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <!-- Default Redirect URL Box (Visible ONLY when Affiliate is ON) -->
                            <div x-show="isAffiliate" class="mb-3">
                                <!-- Display Mode -->
                                <div x-show="!editing" @click="startEdit()"
                                    class="border rounded-2 p-2 px-3 bg-white d-flex align-items-center justify-content-between"
                                    style="cursor: pointer; transition: all 0.2s ease-in-out;">
                                    <span class="text-truncate text-dark fw-medium" x-text="tempUrl || 'Enter redirect URL'" :title="tempUrl"></span>
                                    <button type="button" class="btn btn-icon btn-sm btn-soft-primary" title="Edit URL">
                                        <em class="icon ni ni-edit fs-5"></em>
                                    </button>
                                </div>

                                <!-- Edit Mode -->
                                <div x-show="editing" style="display: none;" class="border rounded-2 p-3 bg-white shadow-sm">
                                    <div class="form-group mb-2">
                                        <label class="form-label mb-1 fs-7 text-muted">URL Address</label>
                                        <input type="url" class="form-control"
                                            x-model="tempUrl" x-ref="urlInput"
                                            placeholder="https://example.com/redirect-link"
                                            @keydown.enter="saveEdit()" @keydown.escape="cancelEdit()">
                                        <div x-show="validationError" class="text-danger small mt-1" x-text="validationError"></div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2 mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary px-3" @click="cancelEdit()">
                                            Cancel
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary px-3" @click="saveEdit()">
                                            <em class="icon ni ni-check me-1"></em> Save
                                        </button>
                                    </div>
                                </div>

                                @error('affiliate_link')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Add Country URL Form (Visible ONLY when Affiliate is ON) -->
                            @if ($showUrlForm)
                                <div x-show="isAffiliate" class="border rounded-2 p-3 mb-3" style="background-color: #f8f9fa;">
                                    <h6 class="mb-3 fs-6 fw-bold">Add New Redirect URL</h6>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Select Country</label>
                                                <select class="form-control" wire:model="selectedCountryForUrl">
                                                    <option value="">Choose a country</option>
                                                    @foreach ($selectedCountries as $countryId)
                                                        @php
                                                            $country = collect($countries)->firstWhere('id', $countryId);
                                                        @endphp
                                                        @if ($country)
                                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('selectedCountryForUrl')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Website URL</label>
                                                <input type="url" class="form-control" wire:model="newWebsiteUrl"
                                                    placeholder="https://example.com" />
                                                @error('newWebsiteUrl')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="form-check form-switch d-flex align-items-center m-0">
                                            <input class="form-check-input" type="checkbox" id="countryAffiliateToggle"
                                                wire:model="countryIsAffiliate" style="width: 2.5rem; height: 1.25rem; cursor: pointer;">
                                            <label class="form-check-label ms-2 mb-0" for="countryAffiliateToggle">
                                                <span class="fw-medium">
                                                    {{ $countryIsAffiliate ? 'Affiliate Link' : 'Direct Link' }}
                                                </span>
                                            </label>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-secondary btn-sm" wire:click="cancelAddUrl">
                                                Cancel
                                            </button>
                                            <button type="button" class="btn btn-success btn-sm" wire:click="addCountryWebsiteUrl"
                                                wire:loading.attr="disabled" wire:target="addCountryWebsiteUrl">
                                                <span wire:loading.remove wire:target="addCountryWebsiteUrl">
                                                    Save URL
                                                </span>
                                                <span wire:loading wire:target="addCountryWebsiteUrl">
                                                    <em class="icon ni ni-spinner spin me-1"></em>Saving...
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Existing Country URLs List (Visible ONLY when Affiliate is ON) -->
                            @if (!empty($countryWebsiteUrls))
                                <div x-show="isAffiliate" class="country-urls-list mt-3">
                                    @foreach ($countryWebsiteUrls as $countryId => $urlData)
                                        @php
                                            $country = collect($countries)->firstWhere('id', $countryId);
                                            $countryDisplayName = $country ? ($country->name . ($country->language ? ' – ' . $country->language->name : '')) : '';
                                        @endphp
                                        @if ($country)
                                            <div class="country-url-group mb-3">
                                                <h6 class="mb-2 fs-6 text-muted fw-bold">{{ $countryDisplayName }}</h6>

                                                @if (is_array($urlData) || is_object($urlData))
                                                    @foreach ((array) $urlData as $index => $urlInfo)
                                                        <div class="url-item border rounded-2 p-3 mb-2 bg-white shadow-sm">
                                                            @if ($editingUrl == $countryId . '-' . $index)
                                                                <!-- Edit Mode -->
                                                                <div class="row g-2 align-items-center">
                                                                    <div class="col-md-9" x-data="{ tempAffiliateCountry: @entangle('editIsAffiliate') }">
                                                                        <div class="form-group mb-2">
                                                                            <input type="url" class="form-control"
                                                                                wire:model="editUrlValue"
                                                                                placeholder="https://example.com">
                                                                            @error('editUrlValue')
                                                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>

                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <span class="small text-muted">Is Affiliate:</span>
                                                                            <div class="form-check form-switch m-0">
                                                                                <input class="form-check-input" type="checkbox"
                                                                                    x-model="tempAffiliateCountry"
                                                                                    style="width: 2.2rem; height: 1.1rem; cursor: pointer;">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-3 text-end">
                                                                        <button type="button" class="btn btn-success btn-sm me-1"
                                                                            wire:click="saveUrlEdit({{ $countryId }}, {{ $index }})"
                                                                            wire:loading.attr="disabled"
                                                                            wire:target="saveUrlEdit({{ $countryId }}, {{ $index }})">
                                                                            <em class="icon ni ni-check"></em>
                                                                        </button>
                                                                        <button type="button" class="btn btn-secondary btn-sm"
                                                                            wire:click="cancelUrlEdit">
                                                                            <em class="icon ni ni-cross"></em>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <!-- Display Mode -->
                                                                <div class="row align-items-center">
                                                                    <div class="col-md-8">
                                                                        <div class="d-flex align-items-center">
                                                                            <em class="icon ni ni-link text-muted me-2 fs-5"></em>
                                                                            <a href="{{ is_array($urlInfo) ? $urlInfo['url'] : $urlInfo }}"
                                                                                target="_blank" class="text-primary text-decoration-none text-truncate">
                                                                                {{ is_array($urlInfo) ? $urlInfo['url'] : $urlInfo }}
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4 d-flex justify-content-end ">
                                                                        <button type="button" class="btn btn-light btn-icon btn-sm text-primary"
                                                                            wire:click="editUrl({{ $countryId }}, {{ $index }})"
                                                                            title="Edit URL" style=" margin-right: 10px;">
                                                                            <em class="icon ni ni-edit"></em>
                                                                        </button>
                                                                        <button type="button" class="btn btn-light btn-icon btn-sm text-danger"
                                                                            wire:loading.attr="disabled"
                                                                            wire:target="removeCountryWebsiteUrl({{ $countryId }}, {{ $index }})"
                                                                            title="Remove URL"
                                                                            onclick="event.preventDefault(); if(confirm('Are you sure you want to remove this URL?')) { @this.removeCountryWebsiteUrl({{ $countryId }}, {{ $index }}) }">
                                                                            <em class="icon ni ni-trash"></em>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                                      <!-- Media Upload Section -->

                                     <div class="card card-bordered mb-3">
                                         <div class="card-inner">
                                             <div class="form-group">
                                                 <label class="form-label">Business Icon (SVG)</label>
                                                 <input type="file"
                                                     class="form-control @error('icon_file') is-invalid @enderror"
                                                     wire:model.live="icon_file" accept=".svg,.png" />

                                                 @if ($iconError)
                                                     <div class="text-danger">{{ $iconError }}</div>
                                                 @elseif ($icon_file)
                                                     <div class="text-success">Icon selected:
                                                         {{ $icon_file->getClientOriginalName() }}</div>
                                                     <img src="{{ $icon_file->temporaryUrl() }}"
                                                         class="img-thumbnail mt-2" width="100"
                                                         alt="New Icon Preview">
                                                 @elseif($editMode && $icon_id)
                                                     <img src="{{ asset($icon_id) }}" class="img-thumbnail mt-2"
                                                         width="100" alt="Existing Icon Preview">
                                                 @endif
                                             </div>
                                         </div>
                                     </div>

                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="form-group d-flex justify-content-between align-items-center">
                                        <label class="form-label">SEO Optimization</label>
                                        {{-- <button type="button" class="btn btn-sm btn-secondary"

                                            wire:click="setFieldIdAndOpenModal('meta', 1002, {{ $businessId ?? 'null' }})"
                                            >
                                            AI Autofill
                                        </button> --}}



                                    </div>
                                    <div class="form-group mt-3">
                                        <label class="form-label">Main meta title</label>
                                        <input type="text" class="form-control"
                                            @error('meta_title') is-invalid @enderror" wire:model.live="meta_title" />
                                        @error('meta_title')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-3">
                                        <label class="form-label">Main meta description</label>
                                        <textarea class="form-control @error('meta_description') is-invalid @enderror" wire:model.live="meta_description"
                                            rows="4"></textarea>
                                        @error('meta_description')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <hr class="preview-hr my-3">

                                    <div class="form-group mt-3">
                                        <label class="form-label">Alternatives Meta Title</label>
                                        <input type="text" class="form-control @error('alternatives_meta_title') is-invalid @enderror"
                                            wire:model.live="alternatives_meta_title" placeholder="" />
                                        @error('alternatives_meta_title')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-3">
                                        <label class="form-label">Alternatives Meta Description</label>
                                        <textarea class="form-control @error('alternatives_meta_description') is-invalid @enderror"
                                            wire:model.live="alternatives_meta_description" rows="2" placeholder=""></textarea>
                                        @error('alternatives_meta_description')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <hr class="preview-hr my-3">

                                    <div class="form-group mt-3">
                                        <label class="form-label">Reviews Meta Title</label>
                                        <input type="text" class="form-control @error('reviews_meta_title') is-invalid @enderror"
                                            wire:model.live="reviews_meta_title" placeholder="" />
                                        @error('reviews_meta_title')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-3">
                                        <label class="form-label">Reviews Meta Description</label>
                                        <textarea class="form-control @error('reviews_meta_description') is-invalid @enderror"
                                            wire:model.live="reviews_meta_description" rows="2" placeholder=""></textarea>
                                        @error('reviews_meta_description')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <hr class="preview-hr my-3">

                                    <div class="form-group mt-3">
                                        <label class="form-label">FAQs Meta Title</label>
                                        <input type="text" class="form-control @error('faqs_meta_title') is-invalid @enderror"
                                            wire:model.live="faqs_meta_title" placeholder="" />
                                        @error('faqs_meta_title')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-3">
                                        <label class="form-label">FAQs Meta Description</label>
                                        <textarea class="form-control @error('faqs_meta_description') is-invalid @enderror"
                                            wire:model.live="faqs_meta_description" rows="2" placeholder=""></textarea>
                                        @error('faqs_meta_description')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <hr class="preview-hr my-3">

                                    <div class="form-group mt-3">
                                        <label class="form-label">Comparison Meta Title</label>
                                        <input type="text" class="form-control @error('comparison_meta_title') is-invalid @enderror"
                                            wire:model.live="comparison_meta_title" placeholder="" />
                                        @error('comparison_meta_title')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-3">
                                        <label class="form-label">Comparison Meta Description</label>
                                        <textarea class="form-control @error('comparison_meta_description') is-invalid @enderror"
                                            wire:model.live="comparison_meta_description" rows="2" placeholder=""></textarea>
                                        @error('comparison_meta_description')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- SEO Keywords Section -->
                                    <div class="form-group mt-3 d-none">
                                        <label class="form-label">Primary Keywords <span class="text-muted">(1-2
                                                keywords)</span></label>
                                        <textarea class="form-control @error('primary_keywords') is-invalid @enderror" wire:model.live="primary_keywords"
                                            rows="2" placeholder="Enter 1-2 main keywords separated by commas"></textarea>
                                        @error('primary_keywords')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-3  d-none">
                                        <label class="form-label">Secondary Keywords <span class="text-muted">(5-10
                                                keywords)</span></label>
                                        <textarea class="form-control @error('secondary_keywords') is-invalid @enderror" wire:model.live="secondary_keywords"
                                            rows="3" placeholder="Enter 5-10 secondary keywords separated by commas"></textarea>
                                        @error('secondary_keywords')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- <div class="form-group mt-3">
                                        <label class="form-label">Long-Tail Keywords <span class="text-muted">(5-10
                                                keywords)</span></label>
                                        <textarea class="form-control @error('long_tail_keywords') is-invalid @enderror" wire:model.live="long_tail_keywords"
                                            rows="3" placeholder="Enter 5-10 long-tail keywords separated by commas"></textarea>
                                        @error('long_tail_keywords')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-3">
                                        <label class="form-label">High-Intent Keywords <span class="text-muted">(5-10
                                                keywords)</span></label>
                                        <textarea class="form-control @error('high_intent_keywords') is-invalid @enderror"
                                            wire:model.live="high_intent_keywords" rows="3"
                                            placeholder="Enter 5-10 high-intent keywords separated by commas"></textarea>
                                        @error('high_intent_keywords')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div> --}}

                                    <hr class="my-3">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <livewire:admin.business-ai-modal />
        </form>
    {{-- @else
        <div>Add business form here</div>

    @endif --}}


</div>



<script>
    document.addEventListener('livewire:init', function() {
        // Main initialization with sufficient delay for DOM to be ready
        setTimeout(initAllSelect2, 50);
        setTimeout(initFeaturesSelect2, 100);
        document.addEventListener("livewire:load", () => {
            initAllSelect2();
        });

        Livewire.hook('message.processed', (message, component) => {
            initAllSelect2();
        });
        Livewire.on('categoryChanged', (categoryFeatures) => {
            initAllSelect2();
            setTimeout(() => {
                updateFeaturesDropdown(categoryFeatures);
                // console.log('running initFeaturesSelect2');
                initFeaturesSelect2();
            }, 200);
        });

        Livewire.on('featuresLoaded', () => {
            console.log('Features loaded event received');
            // Small delay to ensure DOM is updated
            setTimeout(() => {
                initFeaturesSelect2();
            }, 300);
        });

        // Handle form showing and editing events
        Livewire.on('showForm', () => setTimeout(initAllSelect2, 300));
        Livewire.on('businessLoaded', () => setTimeout(initAllSelect2, 300));
        Livewire.hook('morph.updated', (el) => {
            if (!(el instanceof Element)) return;
            if (el.querySelector('.features') || el.classList.contains('features')) {
                console.log('Livewire updated .features, reinitializing Select2');
                setTimeout(() => {
                    initFeaturesSelect2();
                }, 50);
            }
        });
        // Handle validation errors specifically
        Livewire.hook('message.processed', (message, component) => {
            if (message.response && message.response.serverMemo && message.response.serverMemo.errors) {
                const hasErrors = Object.keys(message.response.serverMemo.errors).length > 0;
                if (hasErrors) {
                    console.log('Validation errors detected, reinitializing...');
                    setTimeout(initAllSelect2, 200);
                }
            }
        });

        // Clean up on page navigation
        document.addEventListener('livewire:navigating', destroyAllSelect2);
    });
    /**
     * Initialize all Select2 elements on the page
     */
    function initAllSelect2() {

        $('.lang-supported').select2();

        $('.lang-supported').on('change', function() {
            let data = $(this).val();
            Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).set('lang_supported',
                data);
        });
    }

    function initFeaturesSelect2() {

        const $el = $('.features');

        console.log($el);

        // issue here
        // if ($el.hasClass('select2-hidden-accessible')) return;

        if ($el.hasClass('select2-hidden-accessible')){

            $el.select2('destroy');
        }

        $el.off('change').on('change', function() {
            const selected = $(this).val();

            console.log('selected value');
            console.log(selected);

            @this.set('selectedFeatures', selected);
            //   $el.val(selected).trigger('change');

        });

        $el.select2({
            placeholder: 'Select feature(s)',
            allowClear: true,
            width: '100%',
        });

        $el.val(@this.get('selectedFeatures')).trigger('change');

    }


    function updateFeaturesDropdown(features) {
        const $featuresSelect = $('.features');
        if (!$featuresSelect.length) return;

        // Clear previous options
        $featuresSelect.empty();

        // Check if features are available and populate them
        if (features && features[0] && features[0].length > 0) {
            features[0].forEach((feature) => {
                // Make sure translations exist
                if (feature.translations && feature.translations.length > 0) {
                    const translation = feature.translations[0].name;
                    $featuresSelect.append(new Option(translation, feature.id));
                } else {
                    // If translations are missing, append "Unnamed Feature"
                    $featuresSelect.append(new Option('Unnamed Feature', feature.id));
                }
            });
        } else {
            // If no features are available, add a placeholder
            $featuresSelect.append(new Option('No features available', '', true, false));
        }

        // Reinitialize Select2 with the new options
        $featuresSelect.select2({
            placeholder: 'Select feature(s)',
            allowClear: true,
            width: '100%',
        });

        // Re-apply the selected values (preserve selections)
        const currentValues = $featuresSelect.val() || [];
        $featuresSelect.val(currentValues).trigger('change');
    }

    /**
     * Helper to get initial values from various sources
     */
    function getInitialValues(component, element, wireModelName) {
        // Try multiple sources for the initial value

        // 1. Check for wire:model attribute value
        const wireModel = element.getAttribute('wire:model') ||
            element.getAttribute('wire:model.live');

        if (wireModel && component.$wire.get(wireModel)) {
            return component.$wire.get(wireModel);
        }

        // 2. Check component property with provided wireModelName
        if (wireModelName && component.$wire.get(wireModelName)) {
            return component.$wire.get(wireModelName);
        }

        // 3. Check for selected options in the select element
        const selectedOptions = Array.from(element.selectedOptions || [])
            .map(option => option.value);

        if (selectedOptions.length > 0) {
            return selectedOptions;
        }

        // 4. Check hidden input with same name
        const hiddenInput = document.getElementById(wireModelName + 'Input');
        if (hiddenInput && hiddenInput.value) {
            try {
                return JSON.parse(hiddenInput.value);
            } catch (e) {
                console.error('Error parsing hidden input value:', e);
            }
        }

        return [];
    }

    /**
     * Helper to get the Livewire component from an element
     */
    function getLivewireComponent(element) {
        const closestEl = element.closest('[wire\\:id]');
        if (!closestEl) {
            console.warn('Could not find parent Livewire component');
            return null;
        }

        const componentId = closestEl.getAttribute('wire:id');
        return Livewire.find(componentId);
    }

    /**
     * Destroy a specific Select2 instance
     */
    function destroySelect2(selector) {
        try {
            const elements = document.querySelectorAll(selector);
            elements.forEach(element => {
                if ($(element).hasClass('select2-hidden-accessible')) {
                    $(element).select2('destroy');
                    console.log(`Destroyed Select2 for ${selector}`);
                }
            });
        } catch (e) {
            console.error(`Error destroying Select2 for ${selector}:`, e);
        }
    }
    /**
     * Destroy all Select2 instances
     */
    function destroyAllSelect2() {
        try {
            $('select.select2-hidden-accessible').each(function() {
                $(this).select2('destroy');
            });
            console.log('All Select2 instances destroyed');
        } catch (e) {
            console.error('Error destroying Select2 instances:', e);
        }
    }
</script>
@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        class BusinessFAQReorderManager {
            constructor() {
                this.draggedElement = null;
                this.draggedOver = null;

                // Bind all handlers as arrow functions to preserve `this`
                this.handleDragStart = (e) => {
                    this.draggedElement = e.target.closest('.faq-row');
                    this.draggedElement.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/html', this.draggedElement.outerHTML);
                };

                this.handleDragEnd = () => {
                    if (this.draggedElement) {
                        this.draggedElement.classList.remove('dragging');
                    }

                    document.querySelectorAll('.faq-row').forEach(row => {
                        row.classList.remove('drag-over', 'drag-over-bottom');
                    });

                    this.draggedElement = null;
                    this.draggedOver = null;
                };

                this.handleDragOver = (e) => {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                };

                this.handleDragEnter = (e) => {
                    e.preventDefault();
                    const targetRow = e.target.closest('.faq-row');

                    if (targetRow && targetRow !== this.draggedElement) {
                        this.draggedOver = targetRow;

                        document.querySelectorAll('.faq-row').forEach(row => {
                            row.classList.remove('drag-over', 'drag-over-bottom');
                        });

                        const rect = targetRow.getBoundingClientRect();
                        const mouseY = e.clientY;
                        const middle = rect.top + rect.height / 2;

                        if (mouseY < middle) {
                            targetRow.classList.add('drag-over');
                        } else {
                            targetRow.classList.add('drag-over-bottom');
                        }
                    }
                };

                this.handleDragLeave = (e) => {
                    const targetRow = e.target.closest('.faq-row');
                    if (targetRow) {
                        targetRow.classList.remove('drag-over', 'drag-over-bottom');
                    }
                };

                this.handleDrop = (e) => {
                    e.preventDefault();

                    if (!this.draggedElement || !this.draggedOver) return;

                    const targetRow = this.draggedOver;
                    const tbody = targetRow.closest('tbody');

                    const rect = targetRow.getBoundingClientRect();
                    const mouseY = e.clientY;
                    const middle = rect.top + rect.height / 2;

                    if (mouseY < middle) {
                        tbody.insertBefore(this.draggedElement, targetRow);
                    } else {
                        tbody.insertBefore(this.draggedElement, targetRow.nextSibling);
                    }

                    this.updatePositions(tbody);
                    this.saveOrder(tbody);

                    document.querySelectorAll('.faq-row').forEach(row => {
                        row.classList.remove('drag-over', 'drag-over-bottom', 'dragging');
                    });
                };

                this.init();
            }

            init() {
                this.setupEventListeners();
            }

            setupEventListeners() {
                Livewire.hook('morph.updated', () => {
                    this.initializeDragAndDrop();
                });

                this.initializeDragAndDrop();
            }

            initializeDragAndDrop() {
                document.querySelectorAll('.faq-row').forEach(row => {
                    row.removeEventListener('dragstart', this.handleDragStart);
                    row.removeEventListener('dragend', this.handleDragEnd);
                    row.removeEventListener('dragover', this.handleDragOver);
                    row.removeEventListener('dragenter', this.handleDragEnter);
                    row.removeEventListener('dragleave', this.handleDragLeave);
                    row.removeEventListener('drop', this.handleDrop);

                    row.addEventListener('dragstart', this.handleDragStart);
                    row.addEventListener('dragend', this.handleDragEnd);
                    row.addEventListener('dragover', this.handleDragOver);
                    row.addEventListener('dragenter', this.handleDragEnter);
                    row.addEventListener('dragleave', this.handleDragLeave);
                    row.addEventListener('drop', this.handleDrop);
                });
            }

            updatePositions(tbody) {
                const rows = tbody.querySelectorAll('.faq-row');
                const orderedIds = [];

                rows.forEach((row, index) => {
                    const position = index + 1;
                    row.setAttribute('data-position', position);

                    const badge = row.querySelector('.position-badge');
                    if (badge) {
                        badge.textContent = `#${position}`;
                    }

                    orderedIds.push(row.getAttribute('data-faq-id'));
                });

                return orderedIds;
            }

            saveOrder(tbody) {
                const orderedIds = this.updatePositions(tbody);
                @this.call('reorderFAQs', orderedIds);
            }
        }

        // Initialize if FAQ rows exist
        if (document.querySelector('.faq-row')) {
            window.businessFAQManager = new BusinessFAQReorderManager();
        }
    });
</script>
<script>
    function initCkEditor() {
        const el = document.querySelector('#editor');
        if (!el) return;

        if (el.classList.contains('ck-loaded')) return;
        el.classList.add('ck-loaded');

        ClassicEditor
            .create(el)
            .then(editor => {
                // Store editor instance for cleanup
                window.ckEditorInstance = editor;

                editor.model.document.on('change:data', () => {
                    @this.set('content', editor.getData());
                });
            })
            .catch(error => {
                console.error('CKEditor init error:', error);
            });
    }

    // Cleanup function
    function cleanupCkEditor() {
        if (window.ckEditorInstance) {
            window.ckEditorInstance.destroy();
            window.ckEditorInstance = null;
        }
        const el = document.querySelector('#editor');
        if (el) {
            el.classList.remove('ck-loaded');
        }
    }

    document.addEventListener('livewire:load', initCkEditor);
    document.addEventListener('livewire:update', () => {
        console.log('livewire update running');
        cleanupCkEditor();
        initCkEditor();
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const translateModalEl = document.getElementById('translateModal');
        const translateModal = new bootstrap.Modal(translateModalEl);

        // Show modal via Livewire
        Livewire.on('show-translate-modal', () => {
            translateModal.show();
        });

        // Hide modal via Livewire
        Livewire.on('hide-translate-modal', () => {
            translateModal.hide();
        });

        // Handle translation completion
        Livewire.on('translation-completed', () => {
            // Auto-hide modal after successful translation
            setTimeout(() => {
                translateModal.hide();
            }, 1500);
        });

        // Handle toast notifications using your existing system
        window.addEventListener('show-toast', event => {
            const type = event.detail[0].type || 'info';
            const message = event.detail[0].message || 'No message provided';

            toastr.clear();
            NioApp.Toast(message, type, {
                position: 'top-right'
            });
        });
    });
</script>



@endpush