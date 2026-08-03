<div class="nk-block nk-block-lg all-business-images">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Business Images</h3>
                <div class="nk-block-des text-soft">
                    <p>Manage preview images, screenshot configurations, and automated capture for all businesses.</p>
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-icon alert-dismissible mb-3" role="alert">
            <em class="icon ni ni-check-circle"></em>
            <strong>Success:</strong> {{ session('success') }}
            <button class="close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            @if ($businesses && $businesses->isNotEmpty())
                <table class="datatable-init nowrap nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col" style="width: 30%;"><span class="sub-text">Business Name</span></th>
                            <th class="nk-tb-col" style="width: 55%;"><span class="sub-text">Business Images</span></th>
                            <th class="nk-tb-col tb-tnx-action text-end" style="width: 15%;"><span class="sub-text">Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($businesses as $business)
                            <tr class="nk-tb-item">
                                {{-- Left Column: Business Name --}}
                                <td class="nk-tb-col">
                                    <div class="user-card">
                                        <div class="user-info">
                                            <span class="tb-lead fw-bold" style="font-size: 0.95rem;">
                                                {{ $business->translations->firstWhere('lang_id', $lang_id)?->name ?? $business->translations->first()?->name ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Middle Column: Preview Images Strip --}}
                                <td class="nk-tb-col">
                                    <div class="d-flex align-items-center flex-wrap" style="gap: 10px">
                                        @if (!empty($business->business_images) && count($business->business_images) > 0)
                                            @foreach ($business->business_images as $img)
                                                <div class="border rounded p-1 shadow-sm bg-white" style="width: 90px; height: 60px;">
                                                    <img src="{{ asset($img) }}" 
                                                         alt="Business Image" 
                                                         style="width: 100%; height: 100%; object-fit: cover; border-radius: 3px;">
                                                </div>
                                            @endforeach
                                        @elseif (!empty($business->screenshot_urls) && count(array_filter($business->screenshot_urls)) > 0)
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-light text-primary border px-2 py-1">
                                                    <em class="icon ni ni-link me-1"></em> {{ count(array_filter($business->screenshot_urls)) }} Screenshot URL(s) Configured
                                                </span>
                                                <button type="button" 
                                                        class="btn btn-xs btn-warning text-dark fw-bold" 
                                                        wire:click="openImageModal({{ $business->id }})" 
                                                        title="Click to generate screenshots from configured URLs">
                                                    <em class="icon ni ni-camera me-1"></em> Take Screenshots
                                                </button>
                                            </div>
                                        @else
                                            <span class="badge bg-light text-muted border px-2 py-1">
                                                <em class="icon ni ni-img me-1"></em> No images uploaded
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Right Column: 3 Dots Action Menu --}}
                                <td class="nk-tb-col nk-tb-col-tools text-end">
                                    <ul class="nk-tb-actions gx-1 float-end">
                                        <li>
                                            <div class="dropdown">
                                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                                    <em class="icon ni ni-more-h"></em>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="height:100px">
                                                    <ul class="link-list-opt no-bdr py-1">
                                                        <li>
                                                            <a href="javascript:void(0)" wire:click="openImageModal({{ $business->id }})">
                                                                <em class="icon ni ni-img-fill text-primary"></em><span>Manage Images</span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:void(0)" wire:click="openUrlModal({{ $business->id }})">
                                                                <em class="icon ni ni-link text-info"></em><span>Screenshot URLs</span>
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
            @else
                <div class="text-center py-4">
                    <em class="icon ni ni-building text-muted display-4"></em>
                    <p class="text-muted mt-2">No businesses found.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- MODAL 1: MANAGE BUSINESS IMAGES MODAL --}}
    {{-- ========================================================================= --}}
    <div wire:ignore.self class="modal fade" id="manageImagesModal" tabindex="-1" aria-labelledby="manageImagesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="manageImagesModalLabel">
                        <em class="icon ni ni-img-fill me-1 text-primary"></em> Business Images - {{ $selectedBusinessName }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" wire:click="abortImageModal"></button>
                </div>
                <div class="modal-body p-4">
                    @if (session()->has('modal_success'))
                        <div class="alert alert-success alert-dismissible mb-3">
                            <em class="icon ni ni-check-circle me-1"></em> {{ session('modal_success') }}
                        </div>
                    @endif

                    @if (session()->has('modal_error'))
                        <div class="alert alert-danger alert-dismissible mb-3">
                            <em class="icon ni ni-cross-circle me-1"></em> {{ session('modal_error') }}
                        </div>
                    @endif

                    {{-- Action Header: Take Screenshots --}}
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded bg-light border">
                        <div>
                            <h6 class="mb-0 fw-bold">Automated Screenshot Generator</h6>
                            <small class="text-muted">Take optimized screenshots from configured URLs to replace existing images.</small>
                        </div>
                        <button type="button" class="btn btn-warning text-dark fw-bold" wire:click="takeScreenshots" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="takeScreenshots">
                                <em class="icon ni ni-camera me-1"></em> Take Screenshots
                            </span>
                            <span wire:loading wire:target="takeScreenshots">
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Capturing...
                            </span>
                        </button>
                    </div>

                    {{-- Existing & Preview Images Grid --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-2">Current Image Previews <small class="text-muted">(Max 5 images)</small></label>
                        
                        @if (!empty($currentImages) && count($currentImages) > 0)
                            <div class="row g-3">
                                @foreach ($currentImages as $key => $image)
                                    <div class="col-6 col-md-4">
                                        <div class="position-relative border rounded p-1 bg-white shadow-sm h-100">
                                            <img src="{{ asset($image) }}" class="img-fluid rounded" style="height: 120px; width: 100%; object-fit: cover;">
                                            <button type="button" 
                                                    wire:click="removeCurrentImage({{ $key }})" 
                                                    class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle p-0" 
                                                    style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;"
                                                    title="Remove Image">
                                                &times;
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="border rounded p-3 text-center bg-light text-muted">
                                No images currently selected for this business.
                            </div>
                        @endif
                    </div>

                    {{-- Upload New Manual Images --}}
                    <div class="form-group mb-2">
                        <label class="form-label fw-bold">Upload New Images</label>
                        <input type="file" class="form-control" wire:model="newUploads" multiple accept="image/*">
                        @error('newUploads.*') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    @if (!empty($newUploads) && count($newUploads) > 0)
                        <div class="mt-3">
                            <h6 class="small text-muted fw-bold">New Upload Previews:</h6>
                            <div class="row g-2">
                                @foreach ($newUploads as $key => $file)
                                    <div class="col-4 col-md-3">
                                        <div class="position-relative border rounded p-1 bg-white">
                                            <img src="{{ $file->temporaryUrl() }}" class="img-fluid rounded" style="height: 80px; width:100%; object-fit: cover;">
                                            <button type="button" 
                                                    wire:click="removeNewUpload({{ $key }})" 
                                                    class="btn btn-sm btn-warning position-absolute top-0 end-0 m-1 rounded-circle p-0" 
                                                    style="width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center;">
                                                &times;
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" data-dismiss="modal" wire:click="abortImageModal">
                        <em class="icon ni ni-cross me-1"></em> Abort / Cancel
                    </button>
                    <button type="button" class="btn btn-primary btn-localio" wire:click="saveImages" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveImages"><em class="icon ni ni-save me-1"></em> Save Changes</span>
                        <span wire:loading wire:target="saveImages"><span class="spinner-border spinner-border-sm me-1"></span> Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- MODAL 2: SCREENSHOT URLS CONFIGURATION MODAL --}}
    {{-- ========================================================================= --}}
    <div wire:ignore.self class="modal fade" id="screenshotUrlsModal" tabindex="-1" aria-labelledby="screenshotUrlsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="screenshotUrlsModalLabel">
                        <em class="icon ni ni-link me-1 text-info"></em> Configure Screenshot URLs - {{ $selectedBusinessName }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" wire:click="closeModals"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Specify target URLs from which automated screenshots will be captured. Add 5 or more URLs for complete coverage.
                    </p>

                    @foreach ($screenshotUrls as $index => $url)
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-outline-primary px-2 py-1" style="min-width: 30px;">#{{ $index + 1 }}</span>
                            <input type="url" 
                                   class="form-control" 
                                   wire:model="screenshotUrls.{{ $index }}" 
                                   placeholder="https://example.com/page-{{ $index + 1 }}">
                            <button type="button" 
                                    class="btn btn-sm btn-icon btn-danger" 
                                    wire:click="removeUrlSlot({{ $index }})" 
                                    title="Remove URL">
                                <em class="icon ni ni-trash"></em>
                            </button>
                        </div>
                    @endforeach

                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" wire:click="addUrlSlot">
                        <em class="icon ni ni-plus me-1"></em> Add URL Field
                    </button>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" data-dismiss="modal" wire:click="closeModals">Cancel</button>
                    <button type="button" class="btn btn-success" wire:click="saveScreenshotUrls" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveScreenshotUrls"><em class="icon ni ni-check me-1"></em> Save URLs</span>
                        <span wire:loading wire:target="saveScreenshotUrls"><span class="spinner-border spinner-border-sm me-1"></span> Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        function showModal(id) {
            const modalEl = document.getElementById(id);
            if (!modalEl) return;
            if (window.jQuery && typeof window.jQuery(modalEl).modal === 'function') {
                window.jQuery(modalEl).modal('show');
            } else if (window.bootstrap && window.bootstrap.Modal) {
                const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        }

        function hideModal(id) {
            const modalEl = document.getElementById(id);
            if (!modalEl) return;
            if (window.jQuery && typeof window.jQuery(modalEl).modal === 'function') {
                window.jQuery(modalEl).modal('hide');
            } else if (window.bootstrap && window.bootstrap.Modal) {
                const modal = window.bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }
        }

        Livewire.on('show-manage-images-modal', () => showModal('manageImagesModal'));
        Livewire.on('hide-manage-images-modal', () => hideModal('manageImagesModal'));

        Livewire.on('show-url-modal', () => showModal('screenshotUrlsModal'));
        Livewire.on('hide-url-modal', () => hideModal('screenshotUrlsModal'));
    });
</script>
@endpush
