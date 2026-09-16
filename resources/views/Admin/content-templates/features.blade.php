@extends('admin_layout.master')
@section('content')

<style>
/* Guarantee modals sit in front of the backdrop and remain completely interactive */
.modal-backdrop {
    z-index: 1050 !important;
}
.modal {
    z-index: 1060 !important;
}
#modalTemplateForm {
    z-index: 1070 !important;
}
.modal-dialog {
    z-index: 1065 !important;
    position: relative;
    pointer-events: auto;
}
#modalTemplateForm .modal-dialog {
    z-index: 1075 !important;
    position: relative;
    pointer-events: auto;
}
.modal-content {
    pointer-events: auto;
}
.pagination {
    margin-bottom: 0 !important;
}
.page-item.active .page-link {
    background-color: #0971fe !important;
    border-color: #0971fe !important;
    color: #fff !important;
}
.page-link {
    color: #526484;
}
</style>

<div class="nk-block nk-block-lg">
    {{-- Header Banner --}}
    <div class="nk-block-head d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title mb-1 d-flex align-items-center flex-wrap">
                <span><em class="icon ni ni-cpu text-primary me-2"></em>Content Templates &mdash; Features</span>
                @if(isset($selectedLanguage))
                    <span class="badge bg-primary ms-2 font-weight-normal fs-12px">{{ $selectedLanguage->name }} ({{ $selectedLanguage->lang_code }})</span>
                @endif
            </h4>
            <div class="text-muted small">
                Manage randomized H1 Headline templates for feature landing pages. All other page sections inherit automatically from category definitions.
            </div>
        </div>
        <div class="nk-block-head-content d-flex align-items-center gap-2 mt-2 mt-md-0">
            <form action="{{ route('admin.content-templates.assign-missing') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="section" value="features">
                <input type="hidden" name="lang_id" value="{{ $lang_id }}">
                <button type="submit" class="btn btn-primary btn-sm">
                    <em class="icon ni ni-shuffle me-1"></em> Assign Missing (Random & Lock)
                </button>
            </form>
            <button type="button" class="btn btn-dark btn-sm btn-open-configure" data-toggle="modal" data-target="#modalManageTemplates" data-bs-toggle="modal" data-bs-target="#modalManageTemplates">
                <em class="icon ni ni-setting me-1"></em> Configure H1 Templates
            </button>
            <div class="d-flex align-items-center ms-2">
                <label class="form-label me-2 mb-0 font-weight-bold small text-nowrap">Language:</label>
                <select class="form-select form-select-sm" id="langSelect" style="min-width: 200px;" onchange="window.location.href='{{ route('admin.content-templates.features') }}?lang_id=' + this.value">
                    @if(isset($languages))
                        @foreach ($languages as $lang)
                            <option value="{{ $lang->id }}" {{ (isset($lang_id) && $lang_id == $lang->id) ? 'selected' : '' }}>
                                {{ $lang->name }} ({{ $lang->lang_code }})
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <em class="icon ni ni-check-circle me-1"></em> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" data-bs-dismiss="alert" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </button>
        </div>
    @endif

    {{-- Filter Card --}}
    <div class="card card-bordered mb-4 bg-light border-0 shadow-sm">
        <div class="card-inner py-3">
            <form method="GET" action="{{ route('admin.content-templates.features') }}" class="row g-3 align-items-end">
                <input type="hidden" name="lang_id" value="{{ $lang_id }}">
                <div class="col-md-5">
                    <label class="form-label mb-1 small fw-bold">Select Category / Subcategory</label>
                    <select name="category_id" class="form-select form-select-sm select2">
                        <option value="">All Categories & Subcategories</option>
                        @foreach($allCategories as $cat)
                            @php
                                $cTrans = $cat->categoryTranslations->where('lang_id', $lang_id)->first() ?? $cat->categoryTranslations->first();
                                $cName = $cTrans?->name ?? $cat->name ?? ('Category #' . $cat->id);
                            @endphp
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cName }}
                            </option>
                            @foreach($cat->subCategories as $subCat)
                                @php
                                    $scTrans = $subCat->categoryTranslations->where('lang_id', $lang_id)->first() ?? $subCat->categoryTranslations->first();
                                    $scName = $scTrans?->name ?? $subCat->name ?? ('Category #' . $subCat->id);
                                @endphp
                                <option value="{{ $subCat->id }}" {{ request('category_id') == $subCat->id ? 'selected' : '' }}>
                                    &nbsp;&nbsp;&mdash; {{ $scName }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label mb-1 small fw-bold">Search Feature Name</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="e.g. Free Domain, SSL, Chat Support...">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
                    <a href="{{ route('admin.content-templates.features', ['lang_id' => $lang_id]) }}" class="btn btn-sm btn-light">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Content Table --}}
    <div class="card card-bordered">
        <div class="card-inner p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="featuresTemplatesTable">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th style="min-width: 250px;">Feature Name</th>
                            <th style="min-width: 220px;">Category / Subcategory</th>
                            <th style="min-width: 320px;">Assigned H1 Headline</th>
                            <th class="text-end pe-3" style="min-width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($features as $feature)
                            @php
                                $fTrans = $feature->translations->where('lang_id', $lang_id)->first() ?? $feature->translations->first();
                                $featureName = $fTrans?->name ?? $feature->name ?? ('Feature #' . $feature->id);
                                
                                $catTrans = $feature->category?->categoryTranslations->where('lang_id', $lang_id)->first() ?? $feature->category?->categoryTranslations->first();
                                $categoryName = $catTrans?->name ?? $feature->category?->name ?? 'Category';

                                $asgn = $assignments->get($feature->id);
                                $tpl = $asgn?->template;
                                
                                $resolvedH1 = '';
                                if ($tpl) {
                                    $resolvedH1 = \App\Services\ContentTemplateService::replacePlaceholders($tpl->headline, $feature->category, $feature, $lang_id);
                                }
                            @endphp
                            <tr class="feature-row bg-white">
                                <td class="py-3">
                                    <strong class="fs-14px text-dark d-block">{{ $featureName }}</strong>
                                    <span class="text-muted small">ID: {{ $feature->id }}</span>
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-primary text-white">{{ $categoryName }}</span>
                                </td>
                                <td class="py-3">
                                    @if($tpl)
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-dark text-white">{{ $tpl->option_number }}</span>
                                            <span class="font-monospace text-dark small">{{ $resolvedH1 }}</span>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary text-white fs-11px py-1 px-2">-</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3 py-3">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <button type="button" class="btn btn-sm btn-dark btn-override-feat p-1 px-2"
                                                data-feat-id="{{ $feature->id }}"
                                                data-feat-name="{{ $featureName }}"
                                                data-cat-name="{{ $categoryName }}"
                                                data-selected-tpl="{{ $tpl?->id ?? '' }}"
                                                title="Edit Assignment" data-toggle="tooltip">
                                            <em class="icon ni ni-edit"></em>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger btn-reset-entity p-1 px-2 ms-1"
                                                data-entity-type="feature"
                                                data-entity-id="{{ $feature->id }}"
                                                data-entity-name="{{ $featureName }}"
                                                title="Reset Assignment" data-toggle="tooltip">
                                            <em class="icon ni ni-reload"></em>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    No features found matching the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($features->hasPages())
                <div class="card-inner py-3 border-top d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted small">
                        Showing <strong>{{ $features->firstItem() }}</strong> to <strong>{{ $features->lastItem() }}</strong> of <strong>{{ $features->total() }}</strong> features
                    </div>
                    <div>
                        {{ $features->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
{{-- Modal: Configure Templates --}}
<div class="modal fade" id="modalManageTemplates" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light py-3">
                <h5 class="modal-title d-flex align-items-center">
                    <em class="icon ni ni-setting text-primary me-2"></em> Configure Feature H1 Templates
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </button>
            </div>
            <div class="modal-body p-4">
                {{-- Supported Variables Bar --}}
                <div class="alert alert-info py-5 px-3 mb-3 small">
                    <strong>Supported Variables:</strong> Click any tag to insert into the input:
                    <div class="d-flex flex-wrap gap-1 mt-2">
                        @foreach([
                            '[category_tc]' => 'Web Hosting',
                            '[category_cap]' => 'Web hosting',
                            '[category_lc]' => 'web hosting',
                            '[feature_tc]' => 'Free Domain Privacy',
                            '[feature_cap]' => 'Free domain privacy',
                            '[feature_lc]' => 'free domain privacy',
                            '[entities1_cap]' => 'Web hosting providers',
                            '[year]' => date('Y'),
                            '[country2]' => 'U.S.',
                        ] as $tag => $example)
                            <button type="button" class="btn btn-xs btn-primary btn-tag-insert py-0 px-2" data-tag="{{ $tag }}">{{ $tag }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0 text-dark">Feature H1 Headline Variations</h6>
                        <span class="text-muted small">Defines the primary page title (H1) for category feature landing pages.</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary btn-add-option">
                        <em class="icon ni ni-plus me-1"></em> Add New Option
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light small">
                            <tr>
                                <th style="width: 85px;">Option #</th>
                                <th>H1 Headline Template</th>
                                <th style="width: 140px;" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($templates as $item)
                                <tr>
                                    <td class="font-weight-bold text-center">
                                        <span class="badge bg-dark text-white">#{{ $item->option_number }}</span>
                                    </td>
                                    <td>
                                        <span class="font-monospace text-dark small">{{ $item->headline }}</span>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-xs btn-primary btn-edit-tpl me-1" 
                                                data-id="{{ $item->id }}" 
                                                data-option="{{ $item->option_number }}"
                                                data-headline="{{ htmlspecialchars($item->headline) }}">
                                            <em class="icon ni ni-edit"></em> Edit
                                        </button>
                                        <form action="{{ route('admin.content-templates.delete', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete Option #{{ $item->option_number }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger">
                                                <em class="icon ni ni-trash"></em>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        No template variations defined yet. Click <strong>Add New Option</strong> above.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Add / Edit Template Form --}}
<div class="modal fade" id="modalTemplateForm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="templateEditForm" method="POST" action="{{ route('admin.content-templates.store') }}">
                @csrf
                <input type="hidden" name="section_group" value="features">
                <input type="hidden" name="field_key" value="feature_h1">
                <input type="hidden" name="template_id" id="tpl_id" value="">
                <input type="hidden" name="lang_id" value="{{ $lang_id }}">

                <div class="modal-header bg-light py-3">
                    <h5 class="modal-title" id="tplModalTitle">Add Feature H1 Option</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold small">Feature H1 Headline Template</label>
                        <input type="text" name="headline" id="inputHeadline" class="form-control" placeholder="e.g. Best [category_tc] with [feature_tc] [country2] [year]">
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">Save Template Option</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Override Feature Assignment --}}
<div class="modal fade" id="modalOverrideFeature" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light py-3">
                <h5 class="modal-title d-flex align-items-center">
                    <em class="icon ni ni-tag text-primary me-2"></em> Assign H1 Template &mdash; <span id="featTitle" class="ms-1"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label font-weight-bold small">Select Template Option to Lock:</label>
                    <select class="form-select" id="selectFeatureTemplate">
                        <option value="">-- Select Template Option --</option>
                        @foreach($templates as $tOption)
                            <option value="{{ $tOption->id }}">
                                Option #{{ $tOption->option_number }} &bull; {{ $tOption->headline }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary" id="btnSaveFeatAssign">
                    <em class="icon ni ni-check me-1"></em> Save Lock
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Move modals directly to <body> so they are never trapped in .nk-content / .nk-block stacking contexts
    $('#modalManageTemplates, #modalTemplateForm, #modalViewAssignments, #modalOverrideFeature').appendTo('body');

    // Universal explicit handler for all modal close and cross buttons
    $(document).on('click', '[data-bs-dismiss="modal"], [data-dismiss="modal"], .btn-close, .close', function(e) {
        let targetModal = $(this).closest('.modal');
        if (targetModal.length) {
            e.preventDefault();
            targetModal.modal('hide');
        }
    });

    // Cleanup lingering backdrops when all modals are closed
    $(document).on('hidden.bs.modal', '.modal', function () {
        if ($('.modal.show').length === 0) {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('overflow', '');
        }
    });

    let activeInput = $('#inputHeadline');
    let shouldReopenManageTemplates = false;

    $('#inputHeadline').on('focus', function() {
        activeInput = $(this);
    });

    $('.btn-tag-insert').on('click', function(e) {
        e.preventDefault();
        let tag = $(this).data('tag');
        if (activeInput && activeInput.length) {
            let val = activeInput.val() || '';
            activeInput.val((val ? val + ' ' : '') + tag);
            activeInput.focus();
        }
    });

    

    // Open Configure Templates Modal
    $('.btn-open-configure').on('click', function(e) {
        e.preventDefault();
        $('#modalManageTemplates').modal('show');
    });

    $('.btn-add-option').on('click', function(e) {
        e.preventDefault();
        shouldReopenManageTemplates = true;

        $('#templateEditForm').attr('action', '{{ route("admin.content-templates.store") }}');
        $('#tpl_id').val('');
        $('#inputHeadline').val('');
        $('#tplModalTitle').text('Add Feature H1 Template Option');

        $('#modalManageTemplates').modal('hide');
        setTimeout(function() {
            $('#modalTemplateForm').modal('show');
            setTimeout(function() { $('#inputHeadline').focus(); }, 300);
        }, 350);
    });

    $('.btn-edit-tpl').on('click', function(e) {
        e.preventDefault();
        shouldReopenManageTemplates = true;
        let id = $(this).data('id');
        let opt = $(this).data('option');
        let headline = $(this).data('headline') || '';

        $('#templateEditForm').attr('action', '{{ route("admin.content-templates.update") }}');
        $('#tpl_id').val(id);
        $('#inputHeadline').val(headline);
        $('#tplModalTitle').text('Edit Option #' + opt);

        $('#modalManageTemplates').modal('hide');
        setTimeout(function() {
            $('#modalTemplateForm').modal('show');
            setTimeout(function() { $('#inputHeadline').focus(); }, 300);
        }, 350);
    });

    $('#modalTemplateForm').on('hidden.bs.modal', function() {
        if (shouldReopenManageTemplates) {
            shouldReopenManageTemplates = false;
            setTimeout(function() {
                $('#modalManageTemplates').modal('show');
            }, 200);
        }
    });

    // Reset Entity Assignments
    $('.btn-reset-entity').on('click', function(e) {
        e.preventDefault();
        let entityType = $(this).data('entity-type');
        let entityId = $(this).data('entity-id');
        let entityName = $(this).data('entity-name');

        let msg = 'Do you really want to reset the template assignments for "' + entityName + '"?\n\nThe assigned template will be removed. You can then click "Assign Missing" at the top to re-assign randomly.';
        if (!confirm(msg)) {
            return;
        }

        let btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: '{{ route("admin.content-templates.reset-entity") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                entity_type: entityType,
                entity_id: entityId,
                section_group: 'features',
                lang_id: '{{ $lang_id }}'
            },
            success: function(resp) {
                window.location.reload();
            },
            error: function() {
                btn.prop('disabled', false);
                alert('Failed to reset templates. Please try again.');
            }
        });
    });

    let currentFeatId = null;
    $('.btn-override-feat').on('click', function(e) {
        e.preventDefault();
        currentFeatId = $(this).data('feat-id');
        let featName = $(this).data('feat-name');
        let catName = $(this).data('cat-name');
        let selTpl = $(this).data('selected-tpl');

        $('#featTitle').text(featName + ' (' + catName + ')');
        $('#selectFeatureTemplate').val(selTpl || '');

        $('#modalOverrideFeature').modal('show');
    });

    $('#btnSaveFeatAssign').on('click', function(e) {
        e.preventDefault();
        let templateId = $('#selectFeatureTemplate').val();
        if (!templateId) {
            alert('Please select a template option.');
            return;
        }

        let btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: '{{ route("admin.content-templates.assign-single") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                entity_type: 'feature',
                entity_id: currentFeatId,
                field_key: 'feature_h1',
                template_id: templateId,
                lang_id: {{ $lang_id }}
            },
            success: function(res) {
                btn.prop('disabled', false).html('<em class="icon ni ni-check me-1"></em> Saved');
                setTimeout(() => {
                    location.reload();
                }, 600);
            },
            error: function(err) {
                btn.prop('disabled', false).html('<em class="icon ni ni-check me-1"></em> Save Lock');
                alert('Failed to save assignment.');
            }
        });
    });
});
</script>
@endpush
@endsection
