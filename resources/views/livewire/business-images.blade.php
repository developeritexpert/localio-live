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
                                @php
                                $displayImages = $this->getDisplayImages($business);
                                @endphp

                                @if (!empty($displayImages) && count($displayImages) > 0)

                                @foreach ($displayImages as $img)

                                <div class="border rounded p-1 shadow-sm bg-white"
                                    style="width: 90px; height: 60px;">

                                    <a href="javascript:void(0)"
                                        onclick="document.getElementById('lightbox-img').src='{{ asset($img) }}'; document.getElementById('custom-lightbox').style.display='flex';">

                                        <img src="{{ asset($img) }}"
                                            alt="Business Image"
                                            style="width: 100%; height: 100%; object-fit: cover; border-radius: 3px;">

                                    </a>

                                </div>

                                @endforeach

                                @elseif (
                                empty(trim($business->screenshot_display_category ?? '')) &&
                                !empty($business->screenshot_urls) &&
                                count(array_filter($business->screenshot_urls)) > 0
                                )

                                <div class="d-flex align-items-center gap-2">

                                    <span class="badge bg-light text-primary border px-2 py-1">
                                        <em class="icon ni ni-link me-1"></em>
                                        {{ count(array_filter($business->screenshot_urls)) }}
                                        Screenshot URL(s) Configured
                                    </span>

                                    <button type="button"
                                        class="btn btn-xs btn-warning text-dark fw-bold"
                                        wire:click="openImageModal({{ $business->id }})"
                                        title="Click to generate screenshots from configured URLs">

                                        <em class="icon ni ni-camera me-1"></em>
                                        Take Screenshots

                                    </button>

                                </div>

                                @else

                                <span class="badge bg-light text-muted border px-2 py-1">
                                    <em class="icon ni ni-img me-1"></em>
                                    No images uploaded
                                </span>

                                @endif
                            </div>
                        </td>

                        {{-- Right Column: 3 Dots Action Menu --}}
                        <td class="nk-tb-col nk-tb-col-tools text">
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
                                    <a href="javascript:void(0)" onclick="document.getElementById('lightbox-img').src='{{ asset($image) }}'; document.getElementById('custom-lightbox').style.display='flex';">
                                        <img src="{{ asset($image) }}" class="img-fluid rounded" style="height: 120px; width: 100%; object-fit: cover;">
                                    </a>
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
                                    <a href="javascript:void(0)" onclick="document.getElementById('lightbox-img').src='{{ $file->temporaryUrl() }}'; document.getElementById('custom-lightbox').style.display='flex';">
                                        <img src="{{ $file->temporaryUrl() }}" class="img-fluid rounded" style="height: 80px; width:100%; object-fit: cover;">
                                    </a>
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
    <div wire:ignore.self
        class="modal fade"
        id="screenshotUrlsModal"
        tabindex="-1"
        aria-labelledby="screenshotUrlsModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content shadow-lg border-0">

                {{-- ============================================================= --}}
                {{-- MODAL HEADER                                                   --}}
                {{-- ============================================================= --}}
                <div class="modal-header bg-light">

                    <h5 class="modal-title fw-bold"
                        id="screenshotUrlsModalLabel">

                        <em class="icon ni ni-link me-1 text-info"></em>

                        Configure Screenshot URLs - {{ $selectedBusinessName }}

                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        data-dismiss="modal"
                        aria-label="Close"
                        wire:click="closeModals">
                    </button>

                </div>


                {{-- ============================================================= --}}
                {{-- MODAL BODY                                                     --}}
                {{-- ============================================================= --}}
                <div class="modal-body p-4">


                    {{-- ========================================================= --}}
                    {{-- COUNTRY / REGION                                           --}}
                    {{-- ========================================================= --}}
                    <div class="mb-4">

                        <div class="d-flex align-items-center justify-content-between mb-2">

                            <label class="form-label fw-semibold mb-0">
                                Country / Region
                                <span class="text-danger">*</span>
                            </label>

                            {{-- Manage Categories --}}
                            <button type="button"
                                class="btn btn-outline-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#manageCategoriesModal">
                                <em class="icon ni ni-setting me-1"></em>
                                Manage Categories
                            </button>

                        </div>


                        {{-- Selection Mode --}}
                        <div class="d-flex gap-4 mb-3">

                            <div class="form-check">
                                <input class="form-check-input"
                                    type="radio"
                                    name="countrySelectionMode"
                                    id="countryModeAll"
                                    value="all"
                                    wire:model.live="countrySelectionMode">
                                <label class="form-check-label" for="countryModeAll">
                                    All
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input"
                                    type="radio"
                                    name="countrySelectionMode"
                                    id="countryModeSpecific"
                                    value="specific"
                                    wire:model.live="countrySelectionMode">
                                <label class="form-check-label" for="countryModeSpecific">
                                    Specific Countries/Regions
                                </label>
                            </div>

                        </div>


                        @if ($countrySelectionMode === 'specific')

                        {{-- Search + Select All / Clear All --}}
                        <div class="d-flex align-items-center gap-2 mb-2">

                            <input type="text"
                                class="form-control"
                                placeholder="Search countries..."
                                wire:model.live.debounce.300ms="countrySearch">

                            <button type="button"
                                class="btn btn-primary btn-sm text-nowrap"
                                wire:click="selectAllCountries">
                                Select All
                            </button>

                            <button type="button"
                                class="btn btn-outline-danger btn-sm text-nowrap"
                                wire:click="clearAllCountries">
                                Clear All
                            </button>

                        </div>


                        {{-- Country Checkbox List --}}
                        <div class="border rounded p-2"
                            style="max-height: 220px; overflow-y: auto;">

                            @forelse ($screenshotCountries as $country)
                            @if (!$countrySearch || str_contains(strtolower($country->name), strtolower($countrySearch)))
                            <div class="form-check py-1 border-bottom"
                                wire:key="country-check-{{ $country->id }}">

                                <input class="form-check-input"
                                    type="checkbox"
                                    id="country-{{ $country->id }}"
                                    value="{{ $country->id }}"
                                    wire:model.live="selectedScreenshotCountries">

                                <label class="form-check-label d-block"
                                    for="country-{{ $country->id }}">
                                    {{ $country->name }}
                                </label>

                            </div>
                            @endif
                            @empty
                            <div class="text-muted small text-center py-2">
                                No countries found.
                            </div>
                            @endforelse

                        </div>

                        <div class="form-text">
                            Select one or more countries/regions to configure the screenshot category and URLs.
                        </div>

                        @else

                        <div class="form-text">
                            The screenshot configuration will apply to all countries/regions.
                        </div>

                        @endif

                    </div>


                    {{-- ========================================================= --}}
                    {{-- SCREENSHOT CATEGORY                                         --}}
                    {{-- ========================================================= --}}
                    <div class="mb-4">

                        <label for="screenshotCategory"
                            class="form-label fw-semibold mb-2">

                            Screenshot Category
                            <span class="text-danger">*</span>

                        </label>


                        <select id="screenshotCategory"
                            class="form-select"
                            wire:key="screenshot-category-{{ $selectedBusinessId }}-{{ $selectedScreenshotCountry }}"
                            wire:model.live="selectedScreenshotCategory">

                            <option value="">Default URLs</option>

                            @if (!empty($screenshotCategories))
                            @foreach ($screenshotCategories as $category)
                            <option value="{{ $category['name'] }}">
                                {{ $category['name'] }}
                                @if (!empty($category['is_default']))
                                (Default)
                                @endif
                            </option>
                            @endforeach
                            @endif

                        </select>


                        <div class="form-text">
                            Select a screenshot category assigned to the selected
                            country or region.
                        </div>

                    </div>


                    {{-- Screenshot URL Information --}}
                    <p class="text-muted small mb-3">
                        Specify target URLs from which automated screenshots will be captured.
                        Add 5 or more URLs for complete coverage.
                    </p>


                    {{-- ========================================================= --}}
                    {{-- EXISTING URL FIELDS                                         --}}
                    {{-- ========================================================= --}}
                    <div class="mb-3">

                        @foreach ($screenshotUrls as $index => $url)

                        <div class="d-flex align-items-center gap-2 mb-2">

                            {{-- URL Number --}}
                            <span class="badge bg-outline-primary px-2 py-1"
                                style="min-width: 38px;">

                                #{{ $index + 1 }}

                            </span>


                            {{-- URL Input --}}
                            <input type="url"
                                class="form-control"
                                wire:model.live="screenshotUrls.{{ $index }}"
                                placeholder="https://example.com/page-{{ $index + 1 }}">


                            {{-- Delete URL --}}
                            <button type="button"
                                class="btn btn-sm btn-icon btn-danger"
                                wire:click="removeUrlSlot({{ $index }})"
                                title="Remove URL">

                                <em class="icon ni ni-trash"></em>

                            </button>

                        </div>

                        @endforeach

                    </div>


                    {{-- ========================================================= --}}
                    {{-- ADD URL FIELD                                               --}}
                    {{-- ========================================================= --}}
                    <button type="button"
                        class="btn btn-sm btn-outline-primary"
                        wire:click="addUrlSlot">

                        <em class="icon ni ni-plus me-1"></em>

                        Add URL Field

                    </button>

                </div>


                {{-- ============================================================= --}}
                {{-- MODAL FOOTER                                                   --}}
                {{-- ============================================================= --}}
                <div class="modal-footer bg-light">

                    <button type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal"
                        data-dismiss="modal"
                        wire:click="closeModals">

                        Cancel

                    </button>


                    <button type="button"
                        class="btn btn-success"
                        wire:click="saveScreenshotUrls"
                        wire:loading.attr="disabled">

                        <span wire:loading.remove wire:target="saveScreenshotUrls">

                            <em class="icon ni ni-check me-1"></em>

                            Save URLs

                        </span>


                        <span wire:loading wire:target="saveScreenshotUrls">

                            <span class="spinner-border spinner-border-sm me-1"></span>

                            Saving...

                        </span>

                    </button>

                </div>

            </div>

        </div>

    </div>



    <!-- ============================= -->
    <!-- B. MANAGE SCREENSHOT CATEGORIES -->
    <!-- ============================= -->

    <div wire:ignore.self
        class="modal fade"
        id="manageCategoriesModal"
        tabindex="-1"
        aria-labelledby="manageCategoriesModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content shadow-lg border-0">

                <!-- Modal Header -->
                <div class="modal-header bg-light">

                    <h5 class="modal-title fw-bold"
                        id="manageCategoriesModalLabel">

                        <em class="icon ni ni-folder me-1 text-info"></em>
                        Manage Screenshot Categories

                        @if (!empty($selectedScreenshotCountry))
                        -
                        {{
            collect($screenshotCountries)
                ->firstWhere('id', (int) $selectedScreenshotCountry)
                ?->name
        }}
                        @endif

                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        data-dismiss="modal"
                        aria-label="Close">
                    </button>

                </div>


                <!-- Modal Body -->
                <div class="modal-body p-4">

                    {{-- Country / Region --}}
                    <div class="mb-4">

                        <label class="form-label fw-semibold mb-2">
                            Country / Region
                        </label>

                        <select
                            class="form-select"
                            wire:model.live="selectedScreenshotCountry">

                            <option value="">
                                Select Country / Region
                            </option>

                            @foreach ($screenshotCountries as $country)
                            <option value="{{ $country->id }}">
                                {{ $country->name }}
                            </option>
                            @endforeach

                        </select>

                    </div>

                    <!-- Categories Table -->
                    <div class="table-responsive">


                        @php
                        $totalCategories = count($screenshotCategories);

                        $totalCategoryPages = max(
                        1,
                        (int) ceil($totalCategories / $categoriesPerPage)
                        );

                        $categoryOffset = ($categoryPage - 1) * $categoriesPerPage;

                        $paginatedCategories = array_slice(
                        $screenshotCategories,
                        $categoryOffset,
                        $categoriesPerPage,
                        true
                        );
                        @endphp


                        <table class="table table-bordered align-middle mb-3">

                            <thead class="table-light">

                                <tr>

                                    <th style="width: 80px;">
                                        SR. NO.
                                    </th>

                                    <th>
                                        CATEGORY NAME
                                    </th>

                                    <th style="width: 130px;">
                                        IS DEFAULT
                                    </th>

                                    <th style="width: 120px;">
                                        ACTIONS
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse ($paginatedCategories as $index => $category)

                                <tr>

                                    <td>
                                        {{ $index + 1 }}
                                    </td>

                                    <td>
                                        <span class="fw-medium">
                                            {{ $category['name'] }}
                                        </span>
                                    </td>

                                    <td class="text-center">

                                        @if (!empty($category['is_default']))

                                        <span
                                            class="badge bg-success"
                                            style="cursor: pointer;"
                                            wire:click="setDefaultScreenshotCategory('{{ $category['name'] }}')"
                                            title="Click to remove default">
                                            DEFAULT
                                        </span>

                                        @else

                                        <input
                                            type="radio"
                                            name="defaultCategory"
                                            value="{{ $category['name'] }}"
                                            wire:click="setDefaultScreenshotCategory('{{ $category['name'] }}')">

                                        @endif

                                    </td>

                                    <td class="text-center">

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-icon btn-primary"
                                            wire:click="openEditCategory({{ $index }})"
                                            title="Edit Category"
                                            style="width: 32px; height: 32px;">

                                            <em class="icon ni ni-edit"></em>

                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-icon btn-danger ms-1"
                                            wire:click="openDeleteCategoryModal({{ $index }})"
                                            title="Delete Category"
                                            style="width: 32px; height: 32px;">

                                            <em class="icon ni ni-trash"></em>

                                        </button>

                                    </td>

                                </tr>

                                @empty

                                <tr>
                                    <td colspan="4"
                                        class="text-center text-muted py-4">

                                        No screenshot categories added yet.

                                    </td>
                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                        @if ($totalCategoryPages > 1)

                        <div class="d-flex justify-content-between align-items-center mt-3 mb-4">

                            <!-- Showing information -->
                            <div class="text-muted small">

                                Showing
                                {{ $categoryOffset + 1 }}
                                -
                                {{ min($categoryOffset + $categoriesPerPage, $totalCategories) }}

                                of {{ $totalCategories }} categories

                            </div>

                            <!-- Pagination -->
                            <div class="d-flex align-items-center gap-2">

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    wire:click="previousCategoryPage"
                                    @disabled($categoryPage <=1)>

                                    <em class="icon ni ni-chevron-left"></em>
                                    Previous

                                </button>

                                <span class="small fw-semibold">

                                    Page {{ $categoryPage }}
                                    of {{ $totalCategoryPages }}

                                </span>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    wire:click="nextCategoryPage"
                                    @disabled($categoryPage>= $totalCategoryPages)>

                                    Next
                                    <em class="icon ni ni-chevron-right"></em>

                                </button>

                            </div>

                        </div>

                        @endif

                    </div>

                    {{-- Add New Category --}}
                    <div class="mb-4">

                        <label class="form-label fw-bold mb-2">
                            Add New Category
                        </label>

                        <!-- Input + Button -->
                        <div class="d-flex gap-2 align-items-start">

                            <div class="flex-grow-1">

                                <input
                                    type="text"
                                    class="form-control @error('newScreenshotCategory') is-invalid @enderror"
                                    wire:model.live="newScreenshotCategory"
                                    placeholder="Enter category name">

                            </div>

                            <button
                                type="button"
                                class="btn btn-primary"
                                wire:click="addScreenshotCategory"
                                wire:loading.attr="disabled">

                                <span wire:loading.remove wire:target="addScreenshotCategory">
                                    <em class="icon ni ni-plus me-1"></em>
                                    Add Category
                                </span>

                                <span wire:loading wire:target="addScreenshotCategory">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Adding...
                                </span>

                            </button>

                        </div>

                        <!-- Error Message -->
                        @error('newScreenshotCategory')
                        <div class="text-danger small mt-2">
                            <em class="icon ni ni-alert-circle me-1"></em>
                            {{ $message }}
                        </div>
                        @enderror

                    </div>


                    <!-- Information Note -->
                    <!-- <div class="alert alert-warning mt-3 mb-0">

                        <strong>Note:</strong>

                        <ul class="mb-0 mt-1">

                            <li>
                                One category can be set as <strong>DEFAULT</strong>
                            </li>

                            <li>
                                If no category is created, Default
                                (No Category) will be used automatically
                            </li>

                        </ul>

                    </div> -->

                </div>


                <!-- Modal Footer -->
                <div class="modal-footer bg-light">

                    <button type="button"
                        class="btn btn-outline-secondary"
                        wire:click="closeManageCategoriesModal">
                        Cancel
                    </button>

                </div>

            </div>

        </div>

    </div>




    <!-- ============================= -->
    <!-- C. EDIT SCREENSHOT CATEGORY -->
    <!-- ============================= -->

    <div wire:ignore.self
        class="modal fade"
        id="editScreenshotCategoryModal"
        tabindex="-1"
        aria-labelledby="editScreenshotCategoryModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content shadow-lg border-0">

                <!-- Modal Header -->
                <div class="modal-header bg-light">

                    <h5 class="modal-title fw-bold"
                        id="editScreenshotCategoryModalLabel">

                        <em class="icon ni ni-edit me-1 text-primary"></em>

                        Edit Screenshot Category

                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>

                </div>


                <!-- Modal Body -->
                <div class="modal-body p-4">

                    <div class="mb-3">

                        <label class="form-label fw-bold">
                            Category Name
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            wire:model="editingCategoryName"
                            placeholder="Enter category name">

                    </div>

                </div>


                <!-- Modal Footer -->
                <div class="modal-footer bg-light">

                    <button type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button
                        type="button"
                        class="btn btn-primary"
                        wire:click="updateScreenshotCategory"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="updateScreenshotCategory">
                            <em class="icon ni ni-check me-1"></em>
                            Update Category
                        </span>

                        <span wire:loading wire:target="updateScreenshotCategory">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Updating...
                        </span>
                    </button>

                </div>

            </div>

        </div>

    </div>


    <!-- ============================= -->
    <!-- DELETE CATEGORY CONFIRMATION -->
    <!-- ============================= -->

    <div wire:ignore.self
        class="modal fade"
        id="deleteCategoryModal"
        tabindex="-1"
        aria-labelledby="deleteCategoryModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content shadow-lg border-0">

                <div class="modal-header bg-light">

                    <h5 class="modal-title fw-bold"
                        id="deleteCategoryModalLabel">

                        <em class="icon ni ni-alert-circle me-1 text-danger"></em>
                        Delete Category

                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>

                </div>

                <div class="modal-body text-center p-4">

                    <p class="mb-2">
                        Are you sure you want to delete this category?
                    </p>

                    <h6 class="fw-bold text-danger">
                        {{ $categoryToDeleteName }}
                    </h6>

                    <p class="text-muted small mb-0">
                        This action cannot be undone.
                    </p>

                </div>

                <div class="modal-footer bg-light">

                    <button type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="button"
                        class="btn btn-danger"
                        wire:click="confirmDeleteScreenshotCategory"
                        wire:loading.attr="disabled">

                        <span wire:loading.remove
                            wire:target="confirmDeleteScreenshotCategory">

                            <em class="icon ni ni-trash me-1"></em>
                            Delete Category

                        </span>

                        <span wire:loading
                            wire:target="confirmDeleteScreenshotCategory">

                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Deleting...

                        </span>

                    </button>

                </div>

            </div>

        </div>

    </div>

    <!-- Edit Screenshot Category Modal -->
    <div
        wire:ignore.self
        class="modal fade"
        id="editScreenshotCategoryModal"
        tabindex="-1"
        aria-labelledby="editScreenshotCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">

                <!-- Modal Header -->
                <div class="modal-header bg-light">

                    <h5
                        class="modal-title fw-bold"
                        id="editScreenshotCategoryModalLabel">
                        <em class="icon ni ni-edit me-1 text-info"></em>
                        Edit Screenshot Category
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>

                </div>


                <!-- Modal Body -->
                <div class="modal-body p-4">

                    <div class="mb-3">

                        <label
                            for="editingScreenshotCategoryName"
                            class="form-label fw-bold">
                            Category Name
                        </label>

                        <input
                            type="text"
                            id="editingScreenshotCategoryName"
                            class="form-control"
                            wire:model="editingScreenshotCategoryName"
                            placeholder="Enter category name">

                    </div>

                </div>


                <!-- Modal Footer -->
                <div class="modal-footer bg-light">

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary">
                        <em class="icon ni ni-check me-1"></em>
                        Update Category
                    </button>

                </div>

            </div>
        </div>
    </div>


    <!-- Custom Lightbox Overlay -->
    <div id="custom-lightbox" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 1060; cursor: pointer; align-items: center; justify-content: center;" onclick="this.style.display='none';">
        <span style="position: absolute; top: 20px; right: 30px; color: white; font-size: 40px; font-weight: bold;">&times;</span>
        <img id="lightbox-img" src="" style="max-width: 90%; max-height: 90%; object-fit: contain; border-radius: 5px; box-shadow: 0 5px 25px rgba(0,0,0,0.5);">
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

        Livewire.on('show-edit-category-modal', () => showModal('editScreenshotCategoryModal'));
        Livewire.on('hide-edit-category-modal', () => hideModal('editScreenshotCategoryModal'));

        Livewire.on('show-delete-category-modal', () => showModal('deleteCategoryModal'));
        Livewire.on('hide-delete-category-modal', () => hideModal('deleteCategoryModal'));

        Livewire.on('return-to-screenshot-url-modal', () => {

            hideModal('manageCategoriesModal');

            setTimeout(() => {
                showModal('screenshotUrlsModal');
            }, 150);

        });

        Livewire.on('print-console-logs', (data) => {
            let logs = [];
            if (data && data.logs) {
                logs = data.logs;
            } else if (data && data[0] && data[0].logs) {
                logs = data[0].logs;
            } else if (Array.isArray(data)) {
                logs = data;
            } else if (data && Array.isArray(data[0])) {
                logs = data[0];
            }
            console.warn("--- SCREENSHOT PROCESS LOGS ---");
            logs.forEach(log => {
                if (log.includes('FAILED') || log.includes('failed') || log.includes('error') || log.includes('ERROR') || log.includes('Warning') || log.includes('Exception') || log.includes('Failed')) {
                    console.error("[Screenshot Error] " + log);
                } else {
                    console.log("[Screenshot Info] " + log);
                }
            });
            console.warn("--- END SCREENSHOT PROCESS LOGS ---");
        });
    });
</script>
@endpush