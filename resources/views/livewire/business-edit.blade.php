
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
            enctype="multipart/form-data">
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
                                    <label class="form-label">Pros & cons introduction</label>
                                    <textarea class="form-control" rows="3"
                                        wire:model.live="pro_cons_intro"
                                        placeholder="Introductory text shown above the Pros and Cons boxes..."></textarea>
                                </div>
                            </div>

                            <!-- Heading -->
                            <div class="col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <label class="form-label mb-1">
                                            <strong>Pros & Cons</strong>
                                        </label>
                                        <p class="text-muted small mb-0">
                                            Add Pros (green +) and Cons (red -) for this business.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Pros -->
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3 h-100">

                                    <h6 class="title mb-3">Pros</h6>

                                    @foreach ($businessPros as $index => $pro)
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="text-success fw-bold" style="font-size:18px;">+</span>

                                            <input type="text"
                                                class="form-control"
                                                wire:model.live="businessPros.{{ $index }}.text"
                                                placeholder="e.g. Excellent customer support">

                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    wire:click="removePro({{ $index }})"
                                                    title="Remove">
                                                <em class="icon ni ni-trash"></em>
                                            </button>
                                        </div>
                                    @endforeach

                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary mt-2"
                                            wire:click="addPro">
                                        <em class="icon ni ni-plus"></em> Add Pro
                                    </button>

                                </div>
                            </div>

                            <!-- Cons -->
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3 h-100">

                                    <h6 class="title mb-3">Cons</h6>

                                    @foreach ($businessCons as $index => $con)
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="text-danger fw-bold" style="font-size:18px;">-</span>

                                            <input type="text"
                                                class="form-control"
                                                wire:model.live="businessCons.{{ $index }}.text"
                                                placeholder="e.g. Limited basic plan">

                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    wire:click="removeCon({{ $index }})"
                                                    title="Remove">
                                                <em class="icon ni ni-trash"></em>
                                            </button>
                                        </div>
                                    @endforeach

                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary mt-2"
                                            wire:click="addCon">
                                        <em class="icon ni ni-plus"></em> Add Con
                                    </button>

                                </div>
                            </div>

                            <!-- Summary -->
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label class="form-label">Pros & cons ending text</label>
                                    <textarea class="form-control"
                                            rows="3"
                                            wire:model.live="pro_cons_summary"
                                            placeholder="Summary text shown below the Pros and Cons boxes..."></textarea>
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
                                    <textarea class="form-control" rows="3" wire:model.live="businessOfferings.0.top_text" placeholder="Introduction text above the image..."></textarea>
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
                                <label class="form-label">Alternatives Meta Title</label>
                                <input type="text" class="form-control @error('alternatives_meta_title') is-invalid @enderror"
                                    wire:model.live="alternatives_meta_title" placeholder="Meta title for alternatives page" />
                                @error('alternatives_meta_title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Alternatives Meta Description</label>
                                <textarea class="form-control @error('alternatives_meta_description') is-invalid @enderror"
                                    wire:model.live="alternatives_meta_description" rows="2" placeholder="Meta description for alternatives page"></textarea>
                                @error('alternatives_meta_description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="preview-hr my-3">

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
                                <label class="form-label">Reviews Meta Title</label>
                                <input type="text" class="form-control @error('reviews_meta_title') is-invalid @enderror"
                                    wire:model.live="reviews_meta_title" placeholder="Meta title for reviews page" />
                                @error('reviews_meta_title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Reviews Meta Description</label>
                                <textarea class="form-control @error('reviews_meta_description') is-invalid @enderror"
                                    wire:model.live="reviews_meta_description" rows="2" placeholder="Meta description for reviews page"></textarea>
                                @error('reviews_meta_description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="preview-hr my-3">

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
                                <label class="form-label">FAQs Meta Title</label>
                                <input type="text" class="form-control @error('faqs_meta_title') is-invalid @enderror"
                                    wire:model.live="faqs_meta_title" placeholder="Meta title for FAQs page" />
                                @error('faqs_meta_title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">FAQs Meta Description</label>
                                <textarea class="form-control @error('faqs_meta_description') is-invalid @enderror"
                                    wire:model.live="faqs_meta_description" rows="2" placeholder="Meta description for FAQs page"></textarea>
                                @error('faqs_meta_description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="preview-hr my-3">

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
                                <label class="form-label">Comparison Meta Title</label>
                                <input type="text" class="form-control @error('comparison_meta_title') is-invalid @enderror"
                                    wire:model.live="comparison_meta_title" placeholder="Meta title for comparison page" />
                                @error('comparison_meta_title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Comparison Meta Description</label>
                                <textarea class="form-control @error('comparison_meta_description') is-invalid @enderror"
                                    wire:model.live="comparison_meta_description" rows="2" placeholder="Meta description for comparison page"></textarea>
                                @error('comparison_meta_description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="preview-hr my-3">

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
                            <div class="row g-3">
                                <div class="col-md-12">

                                    


                                    <div class="form-group">

                                        <div class="boxbtn_dlex d-flex justify-content-between mb-2 align-items-center">
                                            <label class="form-label m-0">Redirect URL</label>
                                            <div class="d-flex justify-content-between align-items-center ">
                                                @if (!empty($selectedCountries))
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        wire:click="showAddUrlForm" style="background: #F9633B;">
                                                        <i class="fas fa-plus me-1"></i>Add URL
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        

                                        <div class="affiliate-link-container affiliate-link-container_2 @error('affiliate_link') is-invalid @enderror"
                                            x-data="{
                                                editing: false,
                                                originalUrl: @entangle('affiliate_link').live,
                                                tempUrl: @entangle('affiliate_link').live,
                                                originalAffiliate: @entangle('is_affiliate').live,
                                                tempAffiliate: @entangle('is_affiliate').live,
                                                validationError: '',
                                                init() {
                                                    if ((!this.tempUrl || this.tempUrl.trim() === '') && this.tempAffiliate === null) {
                                                        this.tempAffiliate = true;
                                                    }
                                                },
                                                startEdit() {
                                                    this.editing = true;
                                                    this.originalUrl = this.tempUrl;
                                                    this.originalAffiliate = this.tempAffiliate;
                                                    this.validationError = '';
                                                    this.$nextTick(() => this.$refs.urlInput.focus());
                                                },
                                                saveEdit() {
                                                    this.validationError = '';
                                                    const urlPattern = /^(https?:\/\/)?(localhost(:\d+)?|[\da-z\.-]+\.[a-z\.]{2,6})(:[0-9]+)?(\/[\/\w \.-]*)*\/?(\?[;&a-z\d%_\.~+=-]*)?(#[a-z\d_-]*)?$/i;

                                                    if (this.tempUrl.trim() !== '' && !urlPattern.test(this.tempUrl)) {
                                                        this.validationError = 'Please enter a valid URL';
                                                        return;
                                                    }

                                                    this.editing = false;
                                                },
                                                cancelEdit() {
                                                    this.tempUrl = this.originalUrl;
                                                    this.tempAffiliate = this.originalAffiliate;
                                                    this.validationError = '';
                                                    this.editing = false;
                                                }
                                            }">

                                   <div class="affilates-div">
                                             <div class="affiliate-link-display" x-show="!editing" @click="startEdit()" style="cursor: pointer;">
                                                <div class="url-display-wrapper d-flex align-items-center justify-content-between">
                                                    <span class="url-text" x-text="tempUrl || 'Enter redirect URL'"
                                                        :title="tempUrl"></span>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="affiliate-indicator">
                                                            <template x-if="tempAffiliate">
                                                                <i class="fas fa-check-circle text-success"
                                                                    title="Affiliate Link"></i>
                                                            </template>
                                                            <template x-if="!tempAffiliate">
                                                                <i class="fas fa-exclamation-triangle text-warning"
                                                                    title="Non-Affiliate Link"></i>
                                                            </template>
                                                        </div>
                                                        <em class="icon ni ni-edit text-primary fs-5 btn btn-info" title="Edit"></em>
                                                    </div>
                                                </div>
                                             </div>

                                             <!-- Edit Mode -->
                                             <div class="affiliate-link-edit" x-show="editing" style="display: none;">
                                                 <div class="url-input-wrapper">
                                                     <input type="url" class="form-control affiliate-link-input"
                                                         x-model="tempUrl" x-ref="urlInput"
                                                         placeholder="https://example.com/redirect-link"
                                                         @keydown.enter="saveEdit()" @keydown.escape="cancelEdit()">

                                                     <!-- Validation Error Display -->
                                                     <div x-show="validationError" class="text-danger small mt-1"
                                                         x-text="validationError"></div>
                                                 </div>

                                                 <!-- Toggle Switch - Only visible while editing -->
                                                 <div class="affiliate-toggle-section m-0 mt-2 d-flex align-items-center justify-content-between" style="height: 3rem; z-index: 1;">
                                                     <div class="affiliate-toggle d-flex align-items-center">
                                                         <label class="form-check-label me-2 mb-0">
                                                             Affiliate
                                                         </label>
                                                       <div class="label_from_box d-flex align-items-center">
                                                           <div class="form-check form-switch m-0">
                                                             <input class="form-check-input" type="checkbox"
                                                                 x-model="tempAffiliate" id="affiliateToggle">
                                                         </div>
                                                         <div class="affiliate-indicator-toggle ms-2">
                                                             <template x-if="tempAffiliate">
                                                                 <i class="fas fa-check-circle text-success"
                                                                     title="Affiliate Link"></i>
                                                             </template>
                                                             <template x-if="!tempAffiliate">
                                                                 <i class="fas fa-exclamation-triangle text-warning"
                                                                     title="Non-Affiliate Link"></i>
                                                             </template>
                                                         </div>
                                                       </div>
                                                     </div>

                                                     <!-- Save / Cancel Buttons (Visible only while editing) -->
                                                     <div class="save-cancel-buttons d-flex align-items-center gap-1">
                                                         <button type="button" class="btn btn-sm btn-success px-2 py-1" @click="saveEdit()" title="Save" style="width: 100%; margin-left: -9rem;">
                                                             <em class="icon ni ni-save me-1"></em> Save
                                                         </button>
                                                         <button type="button" class="btn btn-sm btn-secondary px-2 py-1" @click="cancelEdit()" title="Cancel"  style="width: 100%; margin-left: 0rem;">
                                                             <em class="icon ni ni-cross me-1"></em> Cancel
                                                         </button>
                                                     </div>
                                                 </div>
                                             </div>
                                   </div>

                                        </div>

                                        @error('affiliate_link')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="card card-bordered mb-3" style="border:none; ">
                                <div class="card-inner p-0 mt-2">
                                    <!-- Add URL Form -->
                                    @if ($showUrlForm)
                                        <div class="border rounded p-3 mb-3" style="background-color: #f8f9fa;">
                                            <h6 class="mb-3">Add New Redirect URL</h6>
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

                                            <div class="d-flex justify-content-between  mb-3" style="flex-direction: column;">
                                                <div class="form-check form-switch d-flex align-items-center mb-2 p-0">
                                                    <input class="form-check-input" type="checkbox" id="countryAffiliateToggle"
                                                        wire:model="countryIsAffiliate" style="width: 3rem; margin:0px; height: 1.5rem;">
                                                    <label class="form-check-label ms-2" for="countryAffiliateToggle">
                                                        <span class="fw-medium">
                                                            {{ $countryIsAffiliate ? 'Affiliate Link' : 'Direct Link' }}
                                                        </span>
                                                    </label>
                                                </div>
                                                
                                                <div>
                                                    <button type="button" class="btn btn-secondary btn-sm me-2" wire:click="cancelAddUrl">
                                                        Cancel
                                                    </button>
                                                    <button type="button" class="btn btn-success btn-sm" wire:click="addCountryWebsiteUrl"
                                                        wire:loading.attr="disabled" wire:target="addCountryWebsiteUrl">
                                                        <span wire:loading.remove wire:target="addCountryWebsiteUrl">
                                                            Save URL
                                                        </span>
                                                        <span wire:loading wire:target="addCountryWebsiteUrl">
                                                            <i class="fas fa-spinner fa-spin me-1"></i>Saving...
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Display Existing URLs -->
                                    @if (!empty($countryWebsiteUrls))
                                        <div class="country-urls-list">
                                            @foreach ($countryWebsiteUrls as $countryId => $urlData)
                                                 @php
                                                     $country = collect($countries)->firstWhere('id', $countryId);
                                                     $countryDisplayName = $country ? ($country->name . ($country->language ? ' – ' . $country->language->name : '')) : '';
                                                 @endphp
                                                 @if ($country)
                                                     <div class="country-url-group mb-4">
                                                         <div class="d-flex align-items-center mb-2">
                                                             <h6 class="mb-0 me-2">{{ $countryDisplayName }}</h6>
                                                         </div>

                                                        @if (is_array($urlData) || is_object($urlData))
                                                            @foreach ((array) $urlData as $index => $urlInfo)
                                                                <div class="url-item border rounded p-3 mb-2" style="background-color: #fdfdfe;">
                                                                    @if ($editingUrl == $countryId . '-' . $index)
                                                                        <!-- Edit Mode -->
                                                                        <div class="affiliate-link-edit">
                                                                            <div class="row g-2 align-items-center">
                                                                                <!-- Input + Toggle Section -->
                                                                                <!-- Input + Toggle Section -->
                                                                                <div class="col-md-9" x-data="{ tempAffiliate: @entangle('editIsAffiliate') }">
                                                                                    <!-- URL Input -->
                                                                                    <div class="url-input-wrapper">
                                                                                        <input type="url" class="form-control affiliate-link-input"
                                                                                            wire:model="editUrlValue"
                                                                                            placeholder="https://example.com">

                                                                                        <!-- Validation Error -->
                                                                                        @error('editUrlValue')
                                                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>

                                                                                    <!-- Toggle Switch - Only visible while editing -->
                                                                                    <div class="affiliate-toggle-section m-0 mt-2">
                                                                                        <div class="affiliate-toggle" style="width:100%" >
                                                                                            <label class="form-check-label me-2">
                                                                                                Is Affiliate
                                                                                            </label>
                                                                                            <div class="label_from_box d-flex align-items-center">
                                                                                                <div class="form-check form-switch">
                                                                                                    <input class="form-check-input" type="checkbox"
                                                                                                        x-model="tempAffiliate"
                                                                                                        id="affiliateToggle"
                                                                                                        style="width: 2.5rem; height: 1.25rem;">
                                                                                                </div>
                                                                                                <div class="affiliate-indicator-toggle ms-2">
                                                                                                    <template x-if="tempAffiliate">
                                                                                                        <i class="fas fa-check-circle text-success" title="Affiliate Link"></i>
                                                                                                    </template>
                                                                                                    <template x-if="!tempAffiliate">
                                                                                                        <i class="fas fa-exclamation-triangle text-warning" title="Non-Affiliate Link"></i>
                                                                                                    </template>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>


                                                                                <!-- Action Buttons (Right Aligned) -->
                                                                                <div class="col-md-3 text-md-end text-start mt-md-0 mt-2">
                                                                                    <button type="button" class="btn btn-success btn-sm me-1"
                                                                                        wire:click="saveUrlEdit({{ $countryId }}, {{ $index }})"
                                                                                        wire:loading.attr="disabled"
                                                                                        wire:target="saveUrlEdit({{ $countryId }}, {{ $index }})">
                                                                                        <span wire:loading.remove wire:target="saveUrlEdit({{ $countryId }}, {{ $index }})">
                                                                                            <i class="fas fa-check"></i>
                                                                                        </span>
                                                                                        <span wire:loading wire:target="saveUrlEdit({{ $countryId }}, {{ $index }})">
                                                                                            <i class="fas fa-spinner fa-spin"></i>
                                                                                        </span>
                                                                                    </button>
                                                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                                                        wire:click="cancelUrlEdit">
                                                                                        <i class="fas fa-times"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>



                                                                    @else
                                                                        <!-- Display Mode -->
                                                                        <div class="row align-items-center">
                                                                            <div class="col-md-6">
                                                                                <div class="d-flex align-items-center">
                                                                                    <i class="fas fa-link text-muted me-2"></i>
                                                                                    <a href="{{ is_array($urlInfo) ? $urlInfo['url'] : $urlInfo }}"
                                                                                        target="_blank" class="text-decoration-none">
                                                                                        {{ is_array($urlInfo) ? $urlInfo['url'] : $urlInfo }}
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                {{-- @php
                                                                                    $isAffiliate = is_array($urlInfo) ? $urlInfo['is_affiliate'] ?? false : false;
                                                                                @endphp
                                                                                @if ($isAffiliate)
                                                                                    <span class="badge bg-success">
                                                                        <i class="fas fa-handshake me-1"></i>Affiliate
                                                                    </span>
                                                                                @endif --}}
                                                                            </div>
                                                                            {{-- delelte url --}}
                                                                            <div class="col-md-3 d-flex justify-content-end ">
                                                                                <button type="button" class="btn btn-outline-primary btn-sm me-1"
                                                                                    wire:click="editUrl({{ $countryId }}, {{ $index }})"
                                                                                    title="Edit URL">
                                                                                    <i class="fas fa-edit"></i>
                                                                                </button>
                                                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                                                    {{-- wire:click="removeCountryWebsiteUrl({{ $countryId }}, {{ $index }})" --}}
                                                                                    wire:loading.attr="disabled"
                                                                                    wire:target="removeCountryWebsiteUrl({{ $countryId }}, {{ $index }})"
                                                                                    title="Remove URL"
                                                                                    onclick="event.preventDefault(); if(confirm('Are you sure you want to remove this URL?')) { @this.removeCountryWebsiteUrl({{ $countryId }}, {{ $index }}) }">
                                                                                    <span wire:loading.remove wire:target="removeCountryWebsiteUrl({{ $countryId }}, {{ $index }})">
                                                                                        <i class="fas fa-trash"></i>
                                                                                    </span>
                                                                                    <span wire:loading wire:target="removeCountryWebsiteUrl({{ $countryId }}, {{ $index }})">
                                                                                        <i class="fas fa-spinner fa-spin"></i>
                                                                                    </span>
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
                                        <label class="form-label">Meta Title</label>
                                        <input type="text" class="form-control"
                                            @error('meta_title') is-invalid @enderror" wire:model.live="meta_title" />
                                        @error('meta_title')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-3">
                                        <label class="form-label">Meta Description</label>
                                        <textarea class="form-control" @error('meta_description') is-invalid @enderror" wire:model.live="meta_description"
                                            rows="4"></textarea>
                                        @error('meta_description')
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