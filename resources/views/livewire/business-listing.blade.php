<div class="nk-block nk-block-lg all-business">
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
                    {{-- @if ($addbusiness) --}}
                    <div class="toggle-expand-content" data-content="pageMenu">
                        <ul class="nk-block-tools g-3">
                            <li class="nk-block-tools-opt">
                                <a href="#" class="toggle btn btn-icon btn-primary d-md-none">
                                    <em class="icon ni ni-plus"></em>
                                </a>
                                {{-- @if (getCurrentLanguageID() === 1) --}}
                                {{-- <button class="btn btn-primary d-none d-md-inline-flex btn-localio">
                                        <span>Add Businesses</span>
                                    </button> --}}
                                <a type="button" href="{{ route('edit.business') }}"
                                    class="btn btn-primary d-none d-md-inline-flex btn-localio">
                                    <span>Add Businesses</span>
                                </a>

                                {{-- @endif --}}
                            </li>
                        </ul>
                    </div>
                    {{-- @endif --}}
                </div>
            </div>
        </div>
    </div>


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
                                                    data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                <div class="dropdown-menu dropdown-menu-end"
                                                    style="list-style: none; padding: 0; margin: 0; height:auto">
                                                    <ul class="link-list-opt no-bdr">
                                                        <li>
                                                            <a href="{{ route('edit.business', $business->id) }}">
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
                                                            <a href="{{ route('delete.business', $business->id) }}">
                                                                <em class="icon ni ni-trash-fill"></em>Delete
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a href="{{ route('admin.integration.edit', $business->id) }}">
                                                                <em class="icon ni ni-setting-fill"></em>Integration
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

</div>

<script>
    document.addEventListener('livewire:init', function() {
        // Main initialization with sufficient delay for DOM to be ready
        setTimeout(initAllSelect2, 500);
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
        // $('.features, .pricing-options, .lang-supported').select2();
        $('.pricing-options, .lang-supported').select2();

        // $('.features').on('change', function() {
        //     let data = $(this).val();
        //     Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).set('selectedFeatures',
        //         data);
        // });

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

        console.log($el);

        // issue here
        // If Select2 is already initialized, skip
        // if ($el.hasClass('select2-hidden-accessible')) return;
        if ($el.hasClass('select2-hidden-accessible')){

            $el.select2('destroy');
        }

        // Initialize Select2 with dynamic options
        $el.select2({
            placeholder: 'Select feature(s)',
            allowClear: true,
            width: '100%',
        });

        // Listen for changes and update Livewire model
        $el.off('change').on('change', function() {
            const selected = $(this).val();

            console.log('selected value');
            console.log(selected);

            @this.set('selectedFeatures', selected)

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
    // function initSelect2(selector, wireModelName) {
    //     const elements = document.querySelectorAll(selector);
    //     if (!elements.length) {
    //         console.log(`No elements found for selector: ${selector}`);
    //         return;
    //     }

    //     elements.forEach(element => {
    //         // Skip already initialized elements
    //         if ($(element).hasClass('select2-hidden-accessible')) {
    //             return;
    //         }

    //         // Get the Livewire component
    //         const livewireComponent = getLivewireComponent(element);
    //         if (!livewireComponent) return;

    //         try {
    //             $(element).select2({
    //                 placeholder: element.getAttribute('placeholder') || 'Select options',
    //                 allowClear: true,
    //                 width: '100%',
    //                 dropdownParent: $(element).parent()
    //             });

    //             // Set initial values
    //             const initialValues = getInitialValues(livewireComponent, element, wireModelName);
    //             if (initialValues && initialValues.length > 0) {
    //                 $(element).val(initialValues).trigger('change.select2');
    //             }

    //             // Bind change event using Livewire 3 $wire API
    //             $(element).off('change').on('change', function() {
    //                 const values = $(this).val() || [];

    //                 // If wire:model directive is present, prefer that
    //                 const wireModel = element.getAttribute('wire:model') ||
    //                     element.getAttribute('wire:model.live') ||
    //                     wireModelName;

    //                 if (wireModel) {
    //                     livewireComponent.set(wireModel, values);

    //                     // For hidden input compatibility
    //                     const hiddenInput = document.getElementById(wireModelName + 'Input');
    //                     if (hiddenInput) {
    //                         hiddenInput.value = JSON.stringify(values);
    //                         hiddenInput.dispatchEvent(new Event('input', {
    //                             bubbles: true
    //                         }));
    //                     }
    //                 }
    //             });
    //         } catch (error) {
    //             console.error(`Error initializing Select2 for ${selector}:`, error);
    //         }
    //     });
    // }

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
{{-- @push('scripts') --}}
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



{{-- @endpush --}}
