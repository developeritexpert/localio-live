<div class="nk-block nk-block-lg">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Business</h3>
            </div>
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                        <em class="icon ni ni-more-v"></em>
                    </a>
                    @if ($addbusiness)
                        <div class="toggle-expand-content" data-content="pageMenu">
                            <ul class="nk-block-tools g-3">
                                <li class="nk-block-tools-opt">
                                    <a href="#" class="toggle btn btn-icon btn-primary d-md-none">
                                        <em class="icon ni ni-plus"></em>
                                    </a>
                                    @if (getCurrentLanguageID() === 1)
                                        <button wire:click="showAddForm"
                                            class="btn btn-primary d-none d-md-inline-flex btn-localio">
                                            <span>Add Businesses</span>
                                        </button>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if ($editMode)
        <form wire:submit.prevent="{{ $businessId ? 'updateBusiness' : 'storeBusiness' }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-sm btn-secondary" wire:click="fullAutoFill">
                    Full AI Autofill
                </button>
            </div>

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

                    <!-- Business Description Section -->
                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="form-group d-flex justify-content-between align-items-center">
                                <label class="form-label">Business Description</label>
                                <button type="button" class="btn btn-sm btn-secondary"
                                wire:click="setFieldIdAndOpenModal('description', 1000, {{ $businessId ?? 'null' }})">

                                    AI Autofill
                                </button>
                            </div>
                            {{-- <div class="form-group">
                                <textarea class="form-control @error('business_description') is-invalid @enderror"
                                    wire:model.live="business_description" rows="5"></textarea>
                                @error('business_description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div> --}}


                            <!-- If you're using Alpine.js -->
                            <div x-data="{
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
                                        class="form-control description @error('business_description') is-invalid @enderror"
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

                    <!-- Pricing Options Section -->
                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="form-group d-flex justify-content-between align-items-center">
                                <label class="form-label">Pricing Options</label>
                            </div>
                            <div wire:ignore>
                                <select class="form-control pricing-options" multiple
                                    wire:model="selectedPricingOptions">
                                    @foreach ($pricingOptions as $pricingOption)
                                        <option value="{{ $pricingOption->id }}"
                                            {{ in_array($pricingOption->id, $selectedPricingOptions ?? []) ? 'selected' : '' }}>
                                            {{ $pricingOption->translations->first()->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- <input type="hidden" id="selectedPricingOptionsInput" wire:model="selectedPricingOptions"> --}}
                            @error('selectedPricingOptions')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Category Features Section -->
                    @if (!empty($categoryFeatures))
                        <div class="card card-bordered mb-3">
                            <div class="card-inner">
                                <div class="form-group">
                                    <label class="form-label">Business Features</label>
                                    <div wire:ignore>
                                        <select class="form-control features" multiple>
                                            @foreach ($categoryFeatures as $feature)
                                                <option value="{{ $feature->id }}"
                                                    {{ in_array($feature->id, $selectedFeatures ?? []) ? 'selected' : '' }}>
                                                    {{ $feature->translations->first()->name ?? 'Unnamed Feature' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('selectedFeatures')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Company Information Section -->
                    {{-- <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="form-group d-flex justify-content-between align-items-center">
                                <label class="form-label">Company Information</label>

                                {{-- <button type="button" class="btn btn-sm btn-secondary"
                                    wire:click="autoFillCompanyInfo">
                                    AI Autofill
                                </button> --}}

                            {{-- </div>
                            <hr class="my-3">
                            <div class="form-group">
                                <label class="form-label">Headquarters</label>
                                <input type="text" class="form-control @error('headquaters') is-invalid @enderror"
                                    wire:model.live="headquaters" />
                                @error('headquaters')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <hr class="my-3">
                            <div class="form-group">
                                <label class="form-label">Languages Supported</label>
                                <div wire:ignore>
                                    <select class="form-control lang-supported" multiple wire:model="lang_supported">
                                        @foreach ($languages as $language)
                                            <option value="{{ $language->id }}">{{ $language->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('lang_supported')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <hr class="my-3">
                            <div class="form-group">
                                <label class="form-label">Year Founded</label>
                                <input type="text" class="form-control @error('year_found') is-invalid @enderror"
                                    wire:model.live="year_found" />
                                @error('year_found')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <hr class="my-3">
                            <div class="form-group">
                                <label class="form-label">Support Options</label>
                                <input type="text"
                                    class="form-control @error('support_options') is-invalid @enderror"
                                    wire:model.live="support_options" />
                                @error('support_options')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div> --}}

            {{-- business images --}}
            <div class="card card-bordered mb-3">
                <div class="card-inner">
                    <div class="form-group mb-3">
                        <label class="form-label">
                            Upload Business Images <small>(Max 5) - Currently: {{ count($business_images) + count($new_business_images) }}/5</small>
                        </label>

                        <input
                            type="file"
                            class="form-control"
                            wire:model="new_business_images"
                            multiple
                            accept="image/*"
                            @if(count($business_images) >= 5) disabled @endif
                        >

                        {{-- Show validation error --}}
                        @error('new_business_images')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Preview Existing Images --}}
                    @if(count($business_images) > 0)
                        <h6>Saved Images:</h6>
                        <div class="row">
                            @foreach ($business_images as $key => $image)
                                <div class="col-4 mb-2 position-relative">
                                    <img src="{{ asset($image) }}" class="img-thumbnail" style="height: 100px; object-fit: cover;">
                                    <button wire:click="RemoveBusniessImage({{ $key }})" type="button"
                                        class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1">
                                        &times;
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Preview New Uploaded Images --}}
                    @if(count($new_business_images) > 0)
                        <h6>New Images to Save:</h6>
                        <div class="row">
                            @foreach ($new_business_images as $key => $image)
                                <div class="col-4 mb-2 position-relative">
                                    <img src="{{ $image->temporaryUrl() }}" class="img-thumbnail" style="height: 100px; object-fit: cover;">
                                    <button wire:click="removeNewImage({{ $key }})" type="button"
                                        class="btn btn-sm btn-warning position-absolute top-0 end-0 m-1">
                                        &times;
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="text-end mt-4">
                        <button wire:click="BusinessImageSave" class="btn btn-primary"
                            @if(count($new_business_images) == 0) disabled @endif>
                            Save Business Images
                        </button>
                    </div>
                </div>
            </div>


                {{-- Category Topic Descriptions --}}
                {{-- @if ($categoryTopics && $categoryTopics->count())
                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="form-group d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0">Detailed Company Information - Category Topics</label>
                                <button type="button" class="btn btn-sm btn-secondary"
                                wire:click="setFieldIdAndOpenModal('detailtopic', 1003, {{ $businessId ?? 'null' }})">
                                    AI Autofill
                                </button>
                            </div>

                            <hr class="my-3">

                            @foreach ($categoryTopics as $topic)
                            @php
                                $rawTitle = $topic->translations->firstWhere('lang_id', $lang_id)?->title ?? $topic->translations->first()?->title ?? 'Topic ' . $topic->id;
                                $translatedTitle = str_replace('{business_name}', $name ?? 'This Business', $rawTitle);
                            @endphp

                            <div class="form-group">
                                <label class="form-label">{{ $translatedTitle }}</label>
                                <textarea
                                    class="form-control"
                                    rows="3"
                                    wire:model.defer="topicDescriptions.{{ $topic->id }}"
                                    placeholder="Enter description for {{ $translatedTitle }}"></textarea>
                            </div>

                            <hr class="my-3">
                        @endforeach

                        </div>
                    </div>
                @endif     --}}

                @if ($categoryTopics && $categoryTopics->count())
                <div class="card card-bordered mb-3">
                    <div class="card-inner">
                        <div class="form-group d-flex justify-content-between align-items-center">
                            <label class="form-label mb-0">Detailed Company Information - Category Topics</label>
                            <button type="button" class="btn btn-sm btn-secondary"
                            wire:click="setFieldIdAndOpenModal('detailtopic', 1003, {{ $businessId ?? 'null' }})">
                                AI Autofill
                            </button>
                        </div>
                        <hr class="my-3">

                        @foreach ($categoryTopics as $topic)
                            @php
                                $rawTitle = $topic->translations->firstWhere('lang_id', $lang_id)?->title ?? $topic->translations->first()?->title ?? 'Topic ' . $topic->id;
                                $translatedTitle = str_replace('{business_name}', $name ?? 'This Business', $rawTitle);
                            @endphp

                            <div
                                wire:key="topic-{{ $topic->id }}"
                                x-data="{
                                    editor: null,
                                    topicId: {{ $topic->id }},
                                    initCount: 0,
                                    init() {
                                        this.initCount++;
                                        console.log('Alpine init called for topic {{ $topic->id }}, count:', this.initCount);

                                        this.$nextTick(() => {
                                            const element = this.$refs.editor;
                                            console.log('Element for topic {{ $topic->id }}:', element);

                                            if (element.hasAttribute('data-ckeditor-ready')) {
                                                console.log('CKEditor already initialized for topic {{ $topic->id }}');
                                                return;
                                            }

                                            element.setAttribute('data-ckeditor-ready', 'true');

                                            ClassicEditor
                                                .create(element)
                                                .then(editor => {
                                                    console.log('CKEditor created for topic {{ $topic->id }}');
                                                    this.editor = editor;

                                                    // Set initial data
                                                    const initialData = this.$wire.topicDescriptions[this.topicId] || '';
                                                    if (initialData) {
                                                        editor.setData(initialData);
                                                    }

                                                    // Update Livewire on change
                                                    editor.model.document.on('change:data', () => {
                                                        const data = editor.getData();
                                                        console.log('Editor data changed for topic {{ $topic->id }}:', data.substring(0, 50) + '...');
                                                        this.$wire.set('topicDescriptions.' + this.topicId, data);
                                                    });
                                                })
                                                .catch(error => {
                                                    console.error('CKEditor error for topic {{ $topic->id }}:', error);
                                                    element.removeAttribute('data-ckeditor-ready');
                                                });
                                        });
                                    },
                                    destroy() {
                                        console.log('Alpine destroy called for topic {{ $topic->id }}');
                                        if (this.editor) {
                                            this.editor.destroy();
                                            this.editor = null;
                                        }
                                    }
                                }"
                                x-init="init()"
                                x-destroy="destroy()">

                                <div class="form-group">
                                    <label class="form-label">{{ $translatedTitle }}</label>
                                    <textarea
                                        x-ref="editor"
                                        class="form-control"
                                        rows="3"
                                        wire:model.defer="topicDescriptions.{{ $topic->id }}"
                                        placeholder="Enter description for {{ $translatedTitle }}"></textarea>
                                </div>
                            </div>

                            <hr class="my-3">
                        @endforeach

                    </div>
                </div>
            @endif

                </div>

                <div class="col-md-4">
                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between mb-3">
                                        <a href="#" class="btn btn-link text-center"><span><b>View
                                                    Page</b></span></a>
                                        <button type="submit" class="btn btn-primary btn-localio">
                                            <span>{{ $businessId ? 'Update' : 'Save' }}</span>
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
                                    <hr class="my-3">
                                    <div class="form-group position-relative">
                                        <label class="form-label">Country/Region</label>
                                        <div class="position-relative">
                                            <select
                                                class="form-control @error('languages_supported') is-invalid @enderror pe-5"
                                                wire:model.live="languages_supported">
                                                <option value="">Select Region</option>
                                                @foreach ($languages as $language)
                                                    <option value="{{ $language->id }}">{{ $language->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <i class="fa fa-chevron-down position-absolute"
                                                style="right: 15px; top: 50%; transform: translateY(-50%); pointer-events: none;"></i>
                                        </div>
                                        @error('languages_supported')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <hr class="my-3">
                                    <div class="form-group mt-3 position-relative">
                                        <label class="form-label">Business Category</label>
                                        <div class="position-relative">
                                            <select class="form-control pe-5" wire:model.live="selected_category">
                                                <option value=''>Select Category</option>
                                                @if (isset($categories))
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">
                                                            {{ $category->categoryTranslations->first()->name ?? $category->id }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <i class="fa fa-chevron-down position-absolute"
                                                style="right: 15px; top: 50%; transform: translateY(-50%); pointer-events: none;"></i>
                                        </div>
                                        @error('selected_category')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <hr class="my-3">
                                    <div class="form-group mt-3">
                                        <label class="form-label">Permanent Link</label>
                                        <div class="permanent-link-container @error('permanentUrlSlug') is-invalid @enderror"
                                            x-data="{
                                                editing: false,
                                                originalValue: @entangle('permanentUrlSlug').live,
                                                tempValue: @entangle('permanentUrlSlug').live,
                                                startEdit() {
                                                    this.editing = true;
                                                    this.originalValue = this.tempValue;
                                                    this.$nextTick(() => this.$refs.input.focus());
                                                },
                                                saveEdit() {
                                                    if (this.tempValue.trim() === '') {
                                                        this.tempValue = this.originalValue;
                                                        return;
                                                    }
                                                    if (!/^[a-zA-Z0-9\-_]+$/.test(this.tempValue)) {
                                                        alert('Only letters, numbers, hyphens, and underscores are allowed');
                                                        return;
                                                    }
                                                    this.editing = false;
                                                },
                                                cancelEdit() {
                                                    this.tempValue = this.originalValue;
                                                    this.editing = false;
                                                }
                                            }">

                                            <div class="permanent-link-display" x-show="!editing" >
                                                <span class="url-prefix">localio.com/</span>
                                                <span class="url-editable"
                                                    x-text="tempValue || 'your-business-name'"></span>
                                            </div>

                                            <div class="permanent-link-display" x-show="editing"
                                                style="display: none;">
                                                <span class="url-prefix">localio.com/</span>
                                                <input type="text" class="permanent-link-input url-editable"
                                                    x-model="tempValue" x-ref="input" @keydown.enter="saveEdit()"
                                                    @keydown.escape="cancelEdit()">
                                            </div>

                                            <div class="edit-controls">
                                                <i class="fas fa-pencil-alt edit-pencil" x-show="!editing"
                                                    @click="startEdit()" style="cursor: pointer;"></i>

                                                <div class="save-cancel-buttons" x-show="editing"
                                                    style="display: none;">
                                                    <button type="button" class="save-btn" @click="saveEdit()">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="cancel-btn" @click="cancelEdit()">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        @error('permanentUrlSlug')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                        <!-- Hidden field for the full URL -->
                                        <input type="hidden" wire:model="permanent_url">
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
                                                        <div class="form-check py-1 border-bottom">
                                                            <input type="checkbox"
                                                                wire:click="toggleCountrySelection({{ $country->id }})"
                                                                id="country_{{ $country->id }}"
                                                                class="form-check-input"
                                                                {{ in_array($country->id, $selectedCountries) ? 'checked' : '' }}>
                                                            <label class="form-check-label w-100"
                                                                for="country_{{ $country->id }}">
                                                                {{ $country->name }}
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
                                                    style="max-height: 100px; overflow-y: auto;">
                                                    @if (!empty($selectedCountries) && count($selectedCountries) > 0)
                                                        <div class="d-flex flex-wrap gap-2" style="height: 1.75rem;">
                                                            @foreach ($selectedCountries as $countryId)
                                                                @php
                                                                    $selectedCountry = collect($countries)->firstWhere(
                                                                        'id',
                                                                        $countryId,
                                                                    );
                                                                @endphp
                                                                @if ($selectedCountry)
                                                                    <span
                                                                        class="badge bg-primary position-relative m-1"
                                                                        style="padding: 5px 20px 5px 8px; font-size: 0.75rem;">
                                                                        {{ $selectedCountry->name }}
                                                                        <button type="button"
                                                                            wire:click="toggleCountrySelection({{ $countryId }})"
                                                                            class="btn-close btn-close-white position-absolute"
                                                                            style="top: 50%; right: 4px; transform: translateY(-50%); font-size: 0.5rem;"
                                                                            title="Remove {{ $selectedCountry->name }}">
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
                                    <!-- Country-Specific URLs Section -->

                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="card card-bordered mb-3">
                        <div class="card-inner">
                            <div class="row g-3">
                                <div class="col-md-12">

                                    <div class="form-group">


                                            <label class="form-label">AI Reference URL</label>
                                            <div class="permanent-link-container @error('ai_reference_url') is-invalid @enderror"
                                                x-data="{
                                                    editing: false,
                                                    originalValue: @entangle('ai_reference_url').live,
                                                    tempValue: @entangle('ai_reference_url').live,
                                                    startEdit() {
                                                        this.editing = true;
                                                        this.originalValue = this.tempValue;
                                                        this.$nextTick(() => this.$refs.input.focus());
                                                    },
                                                    saveEdit() {
                                                        if (this.tempValue.trim() === '') {
                                                            this.tempValue = this.originalValue;
                                                            return;
                                                        }
                                                        const urlPattern = /^(https?:\/\/)?([\w\-]+\.)+[\w\-]+(\/[\w\-._~:/?#[\]@!$&'()*+,;=]*)?$/;
                                                        if (!urlPattern.test(this.tempValue)) {
                                                            alert('Please enter a valid URL');
                                                            return;
                                                        }
                                                        this.editing = false;
                                                    },
                                                    cancelEdit() {
                                                        this.tempValue = this.originalValue;
                                                        this.editing = false;
                                                    }
                                                }">

                                                <div class="permanent-link-display" x-show="!editing">
                                                    <span class="url-editable"
                                                        x-text="tempValue || 'https://your-business-url.com'"></span>
                                                </div>

                                                <div class="permanent-link-display" x-show="editing" style="display: none;">
                                                    <input type="text" class="permanent-link-input url-editable"
                                                        x-model="tempValue" x-ref="input" @keydown.enter="saveEdit()"
                                                        @keydown.escape="cancelEdit()" placeholder="https://your-business-url.com">
                                                </div>

                                                <div class="edit-controls">
                                                    <i class="fas fa-pencil-alt edit-pencil" x-show="!editing"
                                                        @click="startEdit()" style="cursor: pointer;"></i>

                                                    <div class="save-cancel-buttons" x-show="editing" style="display: none;">
                                                        <button type="button" class="save-btn" @click="saveEdit()">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="cancel-btn" @click="cancelEdit()">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            @error('ai_reference_url')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror

                                            <!-- Hidden field for the bound value -->
                                            <input type="hidden" wire:model="ai_reference_url">
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

                                            <!-- Display Mode -->
                                   <div class="affilates-div">
                                             <div class="affiliate-link-display" x-show="!editing">
                                                <div class="url-display-wrapper">
                                                    <span class="url-text" x-text="tempUrl || 'Enter redirect URL'"
                                                        :title="tempUrl"></span>
                                                    <div class="affiliate-indicator m">
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
                                                <div class="affiliate-toggle-section m-0">
                                                    <div class="affiliate-toggle">
                                                        <label class="form-check-label me-2">
                                                            Is Affiliate
                                                        </label>
                                                      <div class="label_from_box">
                                                          <div class="form-check form-switch">
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
                                                </div>
                                            </div>
                                   </div>

                                            <!-- Edit Controls -->
                                            <div class="edit-controls">
                                                <i class="fas fa-pencil-alt edit-pencil" x-show="!editing"
                                                    @click="startEdit()" style="cursor: pointer;"></i>

                                                <div class="save-cancel-buttons" x-show="editing"
                                                    style="display: none;">
                                                    <button type="button" class="save-btn" @click="saveEdit()">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="cancel-btn" @click="cancelEdit()">
                                                        <i class="fas fa-times"></i>
                                                    </button>
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
                                                @endphp
                                                @if ($country)
                                                    <div class="country-url-group mb-4">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <h6 class="mb-0 me-2">{{ $country->name }}</h6>
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
                                                <label class="form-label">Business Icon</label>
                                                <input type="file"
                                                    class="form-control @error('icon_file') is-invalid @enderror"
                                                    wire:model.live="icon_file" />

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
                                            <div class="form-group">
                                                <label class="form-label">Business Image</label>
                                                <input type="file" class="form-control"
                                                    @error('image_file') is-invalid @enderror"
                                                    wire:model.live="image_file" />

                                                @if ($imageError)
                                                    <div class="text-danger">{{ $imageError }}</div>
                                                @elseif ($image_file)
                                                    <div class="text-success">Image selected:
                                                        {{ $image_file->getClientOriginalName() }}</div>
                                                    <img src="{{ $image_file->temporaryUrl() }}"
                                                        class="img-thumbnail mt-2" width="100"
                                                        alt="New Image Preview">
                                                @elseif($editMode && $image_id)
                                                    <img src="{{ asset($image_id) }}" class="img-thumbnail mt-2"
                                                        width="100" alt="Existing Image Preview">
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
                                        <button type="button" class="btn btn-sm btn-secondary"

                                            wire:click="setFieldIdAndOpenModal('meta', 1002, {{ $businessId ?? 'null' }})"
                                            >
                                            AI Autofill
                                        </button>



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
                                    <div class="form-group mt-3">
                                        <label class="form-label">Primary Keywords <span class="text-muted">(1-2
                                                keywords)</span></label>
                                        <textarea class="form-control @error('primary_keywords') is-invalid @enderror" wire:model.live="primary_keywords"
                                            rows="2" placeholder="Enter 1-2 main keywords separated by commas"></textarea>
                                        @error('primary_keywords')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-3">
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
    @else
    @if ($pageMode !== 'faq' && $pageMode !== 'faq_edit')
        @if ($businesses && $businesses->isNotEmpty())
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="datatable-init nowrap nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                        <thead>
                            <tr class="nk-tb-item nk-tb-head">
                                <th class="nk-tb-col"><span class="sub-text">Name</span></th>
                                <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                                <th class="nk-tb-col"><span class="sub-text">Business Category</span></th>
                                <th class="nk-tb-col"><span class="sub-text">Permanent Link</span></th>
                                <th class="nk-tb-col tb-tnx-action">
                                    <span>Action</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($businesses as $business)
                                <tr class="nk-tb-item">
                                    <td class="nk-tb-col">
                                        <div class="user-card">
                                            <div class="user-info">
                                                <span
                                                    class="tb-lead">{{ $business->translations->first()?->name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="nk-tb-col">
                                        <div class="user-card">
                                            <div class="user-info">
                                                <span class="tb-lead">
                                                    {{ $business->status == 1 ? 'Published' : 'Unpublished' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="nk-tb-col">
                                        <div class="user-card">
                                            <div class="user-info">
                                                <span
                                                    class="tb-lead">{{ $business->category->translations?->name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="nk-tb-col">
                                        <div class="user-card">
                                            <div class="user-info">
                                                <span class="tb-lead">{{ $business->permanent_url ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="nk-tb-col nk-tb-col-tools">
                                        <ul class="nk-tb-actions gx-1">
                                            <li>
                                                <div class="drodown">
                                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                        data-bs-toggle="dropdown"><em
                                                            class="icon ni ni-more-h"></em></a>
                                                    <div class="dropdown-menu dropdown-menu-end"
                                                        style="list-style: none; padding: 0; margin: 0; height:auto">
                                                        <ul class="link-list-opt no-bdr">
                                                            <li>
                                                                <a wire:click="editBusiness({{ $business->id }})">
                                                                    {{-- <a href="{{ route('admin.business.edit', $business->id) }}"> --}}
                                                                    <em class="icon ni ni-edit-fill"></em></em>Edit
                                                                </a>

                                                            </li>
                                                            <li>
                                                                <a href="{{ route('admin.business.faq', $business->id) }}">
                                                                    <em class="icon ni ni-help-fill"></em>Manage FAQs
                                                                </a>
                                                            </li>



                                                            <li>
                                                                <a wire:click="translateBusiness({{ $business->id }})">
                                                                    <em class="icon ni ni-globe"></em></em>Translate
                                                                </a>
                                                            </li>

                                                            <li>
                                                                <a wire:click="deleteBusiness({{ $business->id }})">
                                                                    <em class="icon ni ni-trash-fill"></em>Delete
                                                                </a>
                                                            </li>

                                                        </ul>
                                                    </div>
                                                </div>



                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        {{-- translating modal --}}
            <div wire:ignore.self class="modal fade" id="translateModal" tabindex="-1" aria-labelledby="translateModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="translateModalLabel">Translate Business Content</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="closeModal"></button>
                        </div>

                        <div class="modal-body">
                            <!-- Validation Errors Display -->
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <div class="row gy-4">
                                <!-- Business Info -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label">Business Name</label>
                                        @if($selectedBusiness && $selectedBusiness->translations->isNotEmpty())
                                            <input type="text" class="form-control" value="{{ $selectedBusiness->translations->first()->name }}" readonly>
                                        @else
                                            <input type="text" class="form-control" value="" readonly>
                                        @endif
                                    </div>
                                </div>

                                <!-- Source Language -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Source Language</label>
                                        <select class="form-select" wire:model="sourceLanguage" wire:loading.attr="disabled">
                                            @foreach($sourceLanguages as $language)
                                                <option value="{{ $language['lang_code'] }}">{{ $language['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Target Languages Selection -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label">Target Languages</label>
                                        <!-- Select All Option -->
                                        <div class="form-check mb-3">
                                            <input type="checkbox"
                                                   class="form-check-input"
                                                   id="selectAll"
                                                   wire:model.defer="selectAllLanguages"
                                                   wire:click="toggleAllLanguages"
                                                   wire:loading.attr="disabled">
                                            <label class="form-check-label fw-bold" for="selectAll">Select All Languages</label>
                                        </div>

                                        <!-- Individual Languages -->
                                        <div class="row">
                                            @if(!empty($targetLanguages))
                                                @foreach($targetLanguages as $language)
                                                    <div class="col-md-4 col-sm-6">
                                                        <div class="form-check mb-2">
                                                            <input type="checkbox"
                                                                   class="form-check-input"
                                                                   id="lang_{{ $language['id'] }}"
                                                                   value="{{ $language['id'] }}"
                                                                   wire:model="selectedLanguages"
                                                                    {{-- wire:change="$refresh" --}}
                                                                   wire:loading.attr="disabled">
                                                            <label class="form-check-label" for="lang_{{ $language['id'] }}">{{ $language['name'] }}</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-muted">No target languages available</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Translation Progress -->
                                @if($isTranslating)
                                    <div class="col-12">
                                        <div class="alert alert-info d-flex align-items-center">
                                            <div class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></div>
                                            <div>
                                                <strong>Translating content...</strong> Please wait and do not close this window.
                                            </div>
                                        </div>
                                        <!-- Progress indicator -->
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                 role="progressbar"
                                                 style="width: 100%"
                                                 aria-valuenow="100"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button"
                                    class="btn btn-outline-secondary"
                                    data-bs-dismiss="modal"
                                    wire:click="closeModal"
                                    wire:loading.attr="disabled"
                                    @if($isTranslating) disabled @endif>
                                Cancel
                            </button>
                            <button type="button"
                                    class="btn btn-primary"
                                    wire:click="startTranslation"
                                    wire:loading.attr="disabled"
                                    wire:target="startTranslation"
                                    @if($isTranslating ) disabled @endif>

                                <span wire:loading.remove wire:target="startTranslation">
                                    @if($isTranslating)
                                        <span class="spinner-border spinner-border-sm me-1"></span> Translating...
                                    @else
                                        <em class="icon ni ni-globe me-1"></em> Start Translation
                                    @endif
                                </span>

                                <span wire:loading wire:target="startTranslation">
                                    <span class="spinner-border spinner-border-sm me-1"></span> Translating...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center">
                <button class="btn btn-primary btn-localio">No data found</button>
            </div>
        @endif
    @endif
    @endif

    {{-- faq section --}}
    @if($showFAQSection)

        <div class="nk-block nk-block-lg">

            {{-- Success Message --}}
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Add/Edit FAQ Form --}}
            <div class="card card-bordered mb-4">
                <div class="card-header bg-light">
                    <h4 class="card-title">
                        {{ $editingFAQId !== null ? 'Edit FAQ' : 'Add New FAQ' }}
                    </h4>
                </div>
                <div class="card-inner">
                    <form wire:submit.prevent="{{ $editingFAQId !== null ? 'updateFAQ' : 'addFAQ' }}" class="form-validate">
                        <div class="row g-gs">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="faqQuestion">Question</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control @error('faqQuestion') is-invalid @enderror"
                                            wire:model="faqQuestion" id="faqQuestion" placeholder="Enter FAQ question">
                                    </div>
                                    @error('faqQuestion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="faqAnswer">Answer</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control @error('faqAnswer') is-invalid @enderror"
                                                wire:model="faqAnswer" id="faqAnswer" rows="4"
                                                placeholder="Enter FAQ answer"></textarea>
                                    </div>
                                    @error('faqAnswer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-localio">
                                        <em class="icon ni ni-check"></em>
                                        <span>{{ $editingFAQId !== null ? 'Update FAQ' : 'Save FAQ' }}</span>
                                    </button>

                                    @if($editingFAQId !== null)
                                        <button type="button" class="btn btn-secondary ms-2" wire:click="cancelFAQEdit">
                                            <em class="icon ni ni-cross"></em>
                                            <span>Cancel</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- FAQ List --}}
            <div class="card card-bordered">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Business FAQs</h4>
                    <div class="card-tools">
                        <span class="badge badge-light">{{ count($businessFAQs) }} FAQ(s)</span>
                    </div>
                </div>

                @if(count($businessFAQs) > 0)
                    <div class="card-inner" style="max-height: 600px;">
                        <table class="table nk-tb-list nk-tb-ulist">
                            <thead>
                                <tr class="nk-tb-item nk-tb-head">
                                    <th class="nk-tb-col">Position</th>
                                    <th class="nk-tb-col">Question</th>
                                    <th class="nk-tb-col">Answer</th>
                                    <th class="nk-tb-col tb-tnx-action">Action</th>
                                </tr>
                            </thead>
                            <tbody class="sortable-tbody" id="faq-sortable">
                                @foreach($businessFAQs as $index => $faq)
                                    <tr class="nk-tb-item faq-row" data-faq-id="{{ $faq['id'] }}" data-position="{{ $faq['position'] }}" draggable="true">
                                        <td class="nk-tb-col">
                                            <div class="d-flex align-items-center">
                                                <div class="move-indicator me-2">⋮⋮</div>
                                                <span class="position-badge">#{{ $faq['position'] }}</span>
                                            </div>
                                        </td>
                                        <td class="nk-tb-col">
                                            <div class="user-card">
                                                <div class="user-info">
                                                    <span class="tb-lead">{{ $faq['question'] }}</span>
                                                    @if(!$faq['status'])
                                                        <span class="badge badge-dim badge-warning ms-1">Inactive</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="nk-tb-col">
                                            <span class="tb-sub">{{ Str::limit(strip_tags($faq['answer']), 100) }}</span>
                                        </td>




                                        <td class="nk-tb-col nk-tb-col-tools">
                                            <ul class="nk-tb-actions gx-1">
                                                <li>
                                                    <div class="drodown">
                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                            data-bs-toggle="dropdown"><em
                                                                class="icon ni ni-more-h"></em></a>
                                                        <div class="dropdown-menu dropdown-menu-end edit-btn"
                                                            style="height: auto !important;">
                                                            <ul class="link-list-opt no-bdr">
                                                                <li>
                                                                    <a wire:click="editFAQ({{ $faq['id'] }})">
                                                                        <em class="icon ni ni-edit-fill"></em><span>Edit</span>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a  wire:click="toggleFAQStatus({{ $faq['id'] }})">
                                                                        <em class="icon ni ni-{{ $faq['status'] ? 'eye-off' : 'eye' }}-fill"></em>
                                                                        <span>{{ $faq['status'] ? 'Deactivate' : 'Activate' }}</span>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a wire:click="deleteFAQ({{ $faq['id'] }})" >
                                                                        <em class="icon ni ni-trash-fill"></em><span>Delete</span>
                                                                    </a>
                                                                </li>

                                                            </ul>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </td>


                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="card-inner">
                        <div class="nk-block-content text-center">
                            <div class="nk-block-media mb-3">
                                <em class="icon ni ni-help text-muted" style="font-size: 3rem;"></em>
                            </div>
                            <h5>No FAQs Added Yet</h5>
                            <p class="text-muted">Add your first FAQ using the form above to get started.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>
<script>
    document.addEventListener('livewire:init', function() {
        // Main initialization with sufficient delay for DOM to be ready
        setTimeout(initAllSelect2, 500);
        setTimeout(initFeaturesSelect2, 100);
        document.addEventListener("livewire:load", () => {
            initAllSelect2();
        });

        Livewire.hook('message.processed', () => {
            initAllSelect2();
        });
        Livewire.on('categoryChanged', (categoryFeatures) => {
            initAllSelect2();
            setTimeout(() => {
                updateFeaturesDropdown(categoryFeatures);
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
        $('.features, .pricing-options, .lang-supported').select2();

        $('.features').on('change', function() {
            let data = $(this).val();
            Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).set('selectedFeatures',
                data);
        });

        $('.pricing-options').on('change', function() {
            let data = $(this).val();
            Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).set(
                'selectedPricingOptions', data);
        });

        $('.lang-supported').on('change', function() {
            let data = $(this).val();
            Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).set('lang_supported',
                data);
        });
    }

    function initFeaturesSelect2() {
        const $el = $('.features');
        if (!$el.length) return;

        // If Select2 is already initialized, skip
        if ($el.hasClass('select2-hidden-accessible')) return;

        // Initialize Select2 with dynamic options
        $el.select2({
            placeholder: 'Select feature(s)',
            allowClear: true,
            width: '100%',
        });

        // Listen for changes and update Livewire model
        $el.off('change').on('change', function() {
            const selected = $(this).val();
            Livewire.dispatch('featuresSelected', {
                features: selected,
            });
        });
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
     * Initialize a generic Select2 element with Livewire 3 binding
     */
    function initSelect2(selector, wireModelName) {
        const elements = document.querySelectorAll(selector);
        if (!elements.length) {
            console.log(`No elements found for selector: ${selector}`);
            return;
        }

        elements.forEach(element => {
            // Skip already initialized elements
            if ($(element).hasClass('select2-hidden-accessible')) {
                return;
            }

            // Get the Livewire component
            const livewireComponent = getLivewireComponent(element);
            if (!livewireComponent) return;

            try {
                $(element).select2({
                    placeholder: element.getAttribute('placeholder') || 'Select options',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $(element).parent()
                });

                // Set initial values
                const initialValues = getInitialValues(livewireComponent, element, wireModelName);
                if (initialValues && initialValues.length > 0) {
                    $(element).val(initialValues).trigger('change.select2');
                }

                // Bind change event using Livewire 3 $wire API
                $(element).off('change').on('change', function() {
                    const values = $(this).val() || [];

                    // If wire:model directive is present, prefer that
                    const wireModel = element.getAttribute('wire:model') ||
                        element.getAttribute('wire:model.live') ||
                        wireModelName;

                    if (wireModel) {
                        livewireComponent.set(wireModel, values);

                        // For hidden input compatibility
                        const hiddenInput = document.getElementById(wireModelName + 'Input');
                        if (hiddenInput) {
                            hiddenInput.value = JSON.stringify(values);
                            hiddenInput.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                        }
                    }
                });
            } catch (error) {
                console.error(`Error initializing Select2 for ${selector}:`, error);
            }
        });
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
