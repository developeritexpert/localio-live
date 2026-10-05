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
/* CKEditor styling and z-index */
:root {
    --ck-z-default: 1080;
    --ck-z-modal: 1090;
}
.ck-editor__editable_inline {
    min-height: 140px;
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
    {{-- Header Banner --}}
    <div class="nk-block-head d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title mb-1 d-flex align-items-center flex-wrap">
                <span><em class="icon ni ni-help text-primary me-2"></em>Content Templates &mdash; {{ $title }}</span>
                @if(isset($selectedLanguage))
                    <span class="badge bg-primary ms-2 font-weight-normal fs-12px">{{ $selectedLanguage->name }} ({{ $selectedLanguage->lang_code }})</span>
                @endif
            </h4>
            <div class="text-muted small">
                Define randomized template variations for {{ $title }} headlines and content. Once assigned to a category, options are <strong>permanently locked</strong> in the database.
            </div>
        </div>
        <div class="nk-block-head-content d-flex align-items-center gap-2 mt-2 mt-md-0">
            <form action="{{ route('admin.content-templates.assign-missing') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="section" value="{{ $sectionGroup }}">
                <input type="hidden" name="lang_id" value="{{ $lang_id }}">
                <button type="submit" class="btn btn-primary btn-sm">
                    <em class="icon ni ni-shuffle me-1"></em> Assign Missing (Random & Lock)
                </button>
            </form>
            <button type="button" class="btn btn-dark btn-sm btn-open-configure" data-toggle="modal" data-target="#modalManageTemplates" data-bs-toggle="modal" data-bs-target="#modalManageTemplates">
                <em class="icon ni ni-setting me-1"></em> Configure Templates
            </button>
            <div class="d-flex align-items-center ms-2">
                <label class="form-label me-2 mb-0 font-weight-bold small text-nowrap">Language:</label>
                <select class="form-select form-select-sm" id="langSelect" style="min-width: 200px;" onchange="window.location.href='{{ url()->current() }}?lang_id=' + this.value">
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
        <div class="card-inner py-5">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex flex-wrap gap-3 align-items-center small">
                        @foreach($fieldDefs as $fKey => $fDef)
                            <div class="{{ $loop->first ? '' : 'border-start ps-3' }}">
                                <span class="text-muted d-block">{{ $fDef['label'] }}:</span>
                                <strong class="text-dark filter-count-{{ $fKey }}">{{ $templates->get($fKey)?->count() ?? 0 }} options</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end mt-2 mt-lg-0">
                    <div class="form-control-wrap">
                        <div class="form-icon form-icon-left">
                            <em class="icon ni ni-search"></em>
                        </div>
                        <input type="text" id="categorySearchFilter" class="form-control form-control-sm" placeholder="Search categories...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Table --}}
    <div class="card card-bordered">
        <div class="card-inner p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="faqTemplatesTable">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th style="min-width: 250px;">Category / Subcategory</th>
                            @foreach($fieldDefs as $fDef)
                                <th class="text-center" style="min-width: 150px;">{{ $fDef['label'] }}</th>
                            @endforeach
                            <th class="text-end pe-3" style="min-width: 110px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($parentCategories as $parent)
                            @php
                                $pTrans = $parent->categoryTranslations->where('lang_id', $lang_id)->first() ?? $parent->categoryTranslations->first();
                                $parentName = $pTrans?->name ?? $parent->name ?? ('Category #' . $parent->id);
                                $catAssignments = $assignments->get($parent->id, collect())->keyBy('field_key');
                            @endphp
                            <tr class="category-row bg-white" data-cat-id="{{ $parent->id }}" data-cat-name="{{ strtolower($parentName) }}">
                                <td class="py-3">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary text-white me-2 font-weight-bold">Parent</span>
                                        <div>
                                            <strong class="fs-14px text-dark d-block">{{ $parentName }}</strong>
                                            <span class="text-muted small">#{{ $parent->id }} &bull; {{ $parent->subCategories->count() }} subcategories</span>
                                        </div>
                                    </div>
                                </td>
                                @foreach(array_keys($fieldDefs) as $fKey)
                                    @php
                                        $asgn = $catAssignments->get($fKey);
                                        $tpl = $asgn?->template;
                                    @endphp
                                    <td class="text-center py-3">
                                        @if($tpl)
                                            <span class="fw-bold text-primary cursor-pointer fs-13px" 
                                                  data-toggle="tooltip" data-bs-toggle="tooltip" 
                                                  title="{{ $tpl->headline ? Str::limit($tpl->headline, 70) : 'Option #' . $tpl->option_number }}">
                                                {{ $tpl->option_number }}
                                            </span>
                                        @else
                                            <span class="text-muted fs-13px">-</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-end pe-3 py-3">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <button type="button" class="btn btn-sm btn-dark btn-view-assign p-1 px-2" 
                                                data-cat-id="{{ $parent->id }}" 
                                                data-cat-name="{{ $parentName }}"
                                                data-assignments="{{ json_encode($catAssignments->map(fn($a) => ['template_id' => $a->template_id, 'option_number' => $a->template?->option_number])) }}"
                                                title="Edit Assignments" data-toggle="tooltip">
                                            <em class="icon ni ni-edit"></em>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger btn-reset-entity p-1 px-2 ms-1"
                                                data-entity-type="category"
                                                data-entity-id="{{ $parent->id }}"
                                                data-entity-name="{{ $parentName }}"
                                                title="Reset Assignments" data-toggle="tooltip">
                                            <em class="icon ni ni-reload"></em>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            @foreach ($parent->subCategories as $sub)
                                @php
                                    $sTrans = $sub->categoryTranslations->where('lang_id', $lang_id)->first() ?? $sub->categoryTranslations->first();
                                    $subName = $sTrans?->name ?? $sub->name ?? ('Subcategory #' . $sub->id);
                                    $subAssignments = $assignments->get($sub->id, collect())->keyBy('field_key');
                                @endphp
                                <tr class="category-row bg-light" data-cat-id="{{ $sub->id }}" data-cat-name="{{ strtolower($subName) }}" data-parent-name="{{ strtolower($parentName) }}">
                                    <td class="py-2 ps-4">
                                        <div class="d-flex align-items-center">
                                            <span class="text-muted me-2">&bull;</span>
                                            <div>
                                                <span class="fs-13px text-dark fw-bold d-block">{{ $subName }}</span>
                                                <span class="text-muted small">#{{ $sub->id }} (Sub of {{ $parentName }})</span>
                                            </div>
                                        </div>
                                    </td>
                                    @foreach(array_keys($fieldDefs) as $fKey)
                                        @php
                                            $sAsgn = $subAssignments->get($fKey);
                                            $sTpl = $sAsgn?->template;
                                        @endphp
                                        <td class="text-center py-2">
                                            @if($sTpl)
                                                <span class="fw-bold text-primary cursor-pointer fs-13px"
                                                      data-toggle="tooltip" data-bs-toggle="tooltip" 
                                                      title="{{ $sTpl->headline ? Str::limit($sTpl->headline, 70) : 'Option #' . $sTpl->option_number }}">
                                                    {{ $sTpl->option_number }}
                                                </span>
                                            @else
                                                <span class="text-muted fs-13px">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="text-end pe-3 py-2">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <button type="button" class="btn btn-sm btn-dark btn-view-assign p-1 px-2" 
                                                    data-cat-id="{{ $sub->id }}" 
                                                    data-cat-name="{{ $subName }}"
                                                    data-assignments="{{ json_encode($subAssignments->map(fn($a) => ['template_id' => $a->template_id, 'option_number' => $a->template?->option_number])) }}"
                                                    title="Edit Assignments" data-toggle="tooltip">
                                                <em class="icon ni ni-edit"></em>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger btn-reset-entity p-1 px-2 ms-1"
                                                    data-entity-type="category"
                                                    data-entity-id="{{ $sub->id }}"
                                                    data-entity-name="{{ $subName }}"
                                                    title="Reset Assignments" data-toggle="tooltip">
                                                <em class="icon ni ni-reload"></em>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- Modal: Configure Templates --}}
<div class="modal fade" id="modalManageTemplates" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light py-3">
                <h5 class="modal-title d-flex align-items-center">
                    <em class="icon ni ni-setting text-primary me-2"></em> Configure {{ $title }} Content Templates
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </button>
            </div>
            <div class="modal-body p-4">
                {{-- Supported Variables Bar --}}
                <div class="alert alert-info py-5 px-3 mb-3 small">
                    <strong>Supported Variables:</strong> Click any tag to insert into the active input:
                    <div class="d-flex flex-wrap gap-1 mt-2">
                        @php
                            $tagsList = [
                                '[business]' => 'Bluehost',
                                '[business_cap]' => 'Bluehost',
                                '[business1]' => 'Bluehost',
                                '[business2]' => 'EduNectar',
                                '[business3]' => 'Hostinger',
                                '[business_vs]' => 'Bluehost vs EduNectar',
                                '[businesses]' => 'Bluehost vs EduNectar',
                                '[category]' => 'web hosting',
                                '[category_cap]' => 'Web hosting',
                                '[category_tc]' => 'Web Hosting',
                                '[category_lc]' => 'web hosting',
                                '[entity1_cap]' => 'Web hosting provider',
                                '[entity1_lc]' => 'web hosting provider',
                                '[year]' => date('Y'),
                                '[country1]' => 'United States',
                                '[country2]' => 'U.S.',
                            ];
                            if (isset($sectionGroup) && $sectionGroup !== 'comparison') {
                                $tagsList = [
                                    '[business]' => 'FlowSync Tech',
                                    '[business_cap]' => 'FlowSync Tech',
                                    '[count]' => '3',
                                    '[category_cap]' => 'Web hosting',
                                    '[category_tc]' => 'Web Hosting',
                                    '[category_lc]' => 'web hosting',
                                    '[entity1_cap]' => 'Web hosting provider',
                                    '[entity1_lc]' => 'web hosting provider',
                                    '[entities1_cap]' => 'Web hosting providers',
                                    '[entities1_lc]' => 'web hosting providers',
                                    '[year]' => date('Y'),
                                    '[country1]' => 'United States',
                                    '[country2]' => 'U.S.',
                                ];
                            }
                        @endphp
                        @foreach($tagsList as $tag => $example)
                            <button type="button" class="btn btn-xs btn-primary btn-tag-insert py-0 px-2" data-tag="{{ $tag }}">{{ $tag }}</button>
                        @endforeach
                    </div>
                </div>

                {{-- Field Selector Tabs --}}
                <ul class="nav nav-tabs nav-tabs-mb-icon nav-tabs-card mb-4" id="templateFieldTabs" role="tablist">
                    @foreach($fieldDefs as $key => $def)
                        <li class="nav-item">
                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $key }}" data-toggle="tab" data-bs-toggle="tab" href="#content-{{ $key }}" role="tab">
                                <span>{{ $def['label'] }}</span>
                                <span class="badge bg-primary text-white ms-1">{{ isset($templates[$key]) ? $templates[$key]->count() : 0 }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content" id="templateFieldContent">
                    @foreach($fieldDefs as $key => $def)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="content-{{ $key }}" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-0 text-dark">{{ $def['label'] }}</h6>
                                    <span class="text-muted small">{{ $def['description'] }}</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary btn-add-option" 
                                        data-section="{{ $sectionGroup }}" 
                                        data-field="{{ $key }}"
                                        data-type="{{ $def['type'] }}"
                                        data-f1="{{ $def['field1_label'] }}"
                                        data-f2="{{ $def['field2_label'] ?? '' }}">
                                    <em class="icon ni ni-plus me-1"></em> Add New Option
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light small">
                                        <tr>
                                            <th style="width: 85px;">Option #</th>
                                            <th>{{ $def['field1_label'] }}</th>
                                            @if(($def['type'] ?? '') !== 'single')
                                                <th>{{ $def['field2_label'] ?? 'Subtext / Content' }}</th>
                                            @endif
                                            <th style="width: 140px;" class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($templates[$key] ?? [] as $item)
                                            <tr>
                                                <td class="font-weight-bold text-center">
                                                    <span class="badge bg-dark text-white">#{{ $item->option_number }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-dark small">{{ Str::limit(strip_tags(html_entity_decode($item->headline)), 150) }}</span>
                                                </td>
                                                @if(($def['type'] ?? '') !== 'single')
                                                    <td>
                                                        <span class="text-muted small">{{ Str::limit(strip_tags(html_entity_decode($item->subtext)), 150) }}</span>
                                                    </td>
                                                @endif
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-xs btn-primary btn-edit-tpl me-1" 
                                                            data-id="{{ $item->id }}" 
                                                            data-option="{{ $item->option_number }}"
                                                            data-type="{{ $def['type'] }}"
                                                            data-f1="{{ $def['field1_label'] }}"
                                                            data-f2="{{ $def['field2_label'] ?? '' }}">
                                                        <em class="icon ni ni-edit"></em> Edit
                                                    </button>
                                                    <textarea id="tpl_headline_{{ $item->id }}" class="d-none">{{ $item->headline }}</textarea>
                                                    <textarea id="tpl_subtext_{{ $item->id }}" class="d-none">{{ $item->subtext }}</textarea>
                                                    <form action="{{ route('admin.content-templates.delete', $item->id) }}" method="POST" class="d-inline form-delete-template" data-option="{{ $item->option_number }}" data-field="{{ $key }}">
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
                                                <td colspan="4" class="text-center py-4 text-muted">
                                                    No template variations defined yet for this section. Click <strong>Add New Option</strong> above.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="templateEditForm" method="POST" action="{{ route('admin.content-templates.store') }}">
                @csrf
                <input type="hidden" name="section_group" id="tpl_section_group" value="{{ $sectionGroup }}">
                <input type="hidden" name="field_key" id="tpl_field_key" value="">
                <input type="hidden" name="template_id" id="tpl_id" value="">
                <input type="hidden" name="lang_id" value="{{ $lang_id }}">

                <div class="modal-header bg-light py-3">
                    <h5 class="modal-title" id="tplModalTitle">Add Template Option</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold small" id="lblField1">Headline (H2)</label>
                        <input type="text" name="headline" id="inputHeadline" class="form-control" placeholder="Enter headline template...">
                    </div>
                    <div class="mb-3" id="wrapSubtext">
                        <label class="form-label font-weight-bold small" id="lblField2">Subtext / Content</label>
                        <textarea name="subtext" id="inputSubtext" class="form-control rich-editor" rows="5" placeholder="Enter subtext or rich HTML template..."></textarea>
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

{{-- Modal: View / Override Category Assignments --}}
<div class="modal fade" id="modalViewAssignments" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light py-3">
                <h5 class="modal-title d-flex align-items-center">
                    <em class="icon ni ni-help text-primary me-2"></em> Locked FAQ Assignments &mdash; <span id="assignCatTitle" class="ms-1"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">
                    Below are the currently locked FAQ template options for this category. You can re-assign or select a different option and save to update the lock.
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light small">
                            <tr>
                                <th style="width: 40%;">Section</th>
                                <th>Assigned Option</th>
                                <th style="width: 140px;" class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="assignTableBody">
                            @foreach($fieldDefs as $fKey => $fDef)
                                <tr data-field="{{ $fKey }}">
                                    <td class="fw-bold fs-13px text-dark">{{ $fDef['label'] }}</td>
                                    <td>
                                        <select class="form-select form-select-sm select-assign-option" data-field="{{ $fKey }}">
                                            <option value="">-- Select Option --</option>
                                            @foreach($templates[$fKey] ?? [] as $tOption)
                                                <option value="{{ $tOption->id }}">
                                                    Option #{{ $tOption->option_number }} &bull; {{ Str::limit(strip_tags(html_entity_decode($tOption->headline)), 50) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-primary btn-save-single-assign" data-field="{{ $fKey }}">
                                            <em class="icon ni ni-check me-1"></em> Save Lock
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
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
    let subtextEditor = null;

    @if(session('open_manage_field'))
        $('#modalManageTemplates').modal('show');
        $('#tab-{{ session("open_manage_field") }}').tab('show');
    @endif

    // AJAX Template Option Deletion
    $(document).on('submit', '.form-delete-template', function(e) {
        e.preventDefault();
        let form = $(this);
        let optNum = form.data('option') || '';
        let fieldKey = form.data('field');

        if (!confirm('Delete Option #' + optNum + '?')) {
            return;
        }

        let submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response && response.success) {
                    let row = form.closest('tr');
                    let tbody = row.closest('tbody');
                    row.fadeOut(200, function() {
                        $(this).remove();
                        if (response.templates && response.templates.length > 0) {
                            tbody.find('tr').each(function(idx) {
                                let newNum = idx + 1;
                                $(this).find('td:first .badge').text('#' + newNum);
                                $(this).find('.btn-edit-tpl').data('option', newNum);
                                $(this).find('.form-delete-template').data('option', newNum);
                            });
                        } else {
                            tbody.html('<tr><td colspan="4" class="text-center py-4 text-muted">No template variations defined yet for this section. Click <strong>Add New Option</strong> above.</td></tr>');
                        }

                        let tabBadge = $('#tab-' + fieldKey + ' .badge');
                        if (tabBadge.length) {
                            tabBadge.text(response.remaining_count);
                        }

                        let filterStrong = $('.filter-count-' + fieldKey);
                        if (filterStrong.length) {
                            filterStrong.text(response.remaining_count + ' options');
                        }
                    });
                } else {
                    location.reload();
                }
            },
            error: function() {
                submitBtn.prop('disabled', false).html('<em class="icon ni ni-trash"></em>');
                alert('Failed to delete template option. Please try again.');
            }
        });
    });

        function initSubtextEditor() {
        const el = document.querySelector('#inputSubtext');
        if (!el || typeof ClassicEditor === 'undefined' || subtextEditor) return;

        ClassicEditor
            .create(el, {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo']
            })
            .then(editor => {
                subtextEditor = editor;
                el.ckeditorInstance = editor;

                editor.model.document.on('change:data', () => {
                    el.value = editor.getData();
                });

                editor.ui.focusTracker.on('change:isFocused', (evt, name, isFocused) => {
                    if (isFocused) {
                        activeInput = $('#inputSubtext');
                    }
                });
            })
            .catch(error => {
                console.error("CKEditor Init Error:", error);
            });
    }

    // Initialize CKEditor for Subtext / Content only
    initSubtextEditor();

    $('#lblField1').on('click', function() {
        $('#inputHeadline').focus();
    });

    $('#inputHeadline').on('focus', function() {
        activeInput = $(this);
    });

    $('#lblField2').on('click', function() {
        if (subtextEditor) {
            subtextEditor.editing.view.focus();
            activeInput = $('#inputSubtext');
        }
    });

    $('.btn-tag-insert').on('click', function(e) {
        e.preventDefault();
        let tag = $(this).data('tag');
        if (activeInput && activeInput.attr('id') === 'inputSubtext' && subtextEditor) {
            try {
                subtextEditor.model.change(writer => {
                    subtextEditor.model.insertContent(writer.createText(tag + ' '));
                });
            } catch(err) {
                let cur = subtextEditor.getData() || '';
                subtextEditor.setData(cur + (cur ? ' ' : '') + tag);
            }
            subtextEditor.editing.view.focus();
        } else if (activeInput && activeInput.length) {
            let el = activeInput[0];
            if (el && typeof el.selectionStart === 'number' && typeof el.selectionEnd === 'number') {
                let start = el.selectionStart;
                let end = el.selectionEnd;
                let val = activeInput.val() || '';
                let insert = (start > 0 && val.charAt(start - 1) !== ' ' ? ' ' : '') + tag + ' ';
                activeInput.val(val.slice(0, start) + insert + val.slice(end));
                let newPos = start + insert.length;
                el.setSelectionRange(newPos, newPos);
            } else {
                let val = activeInput.val() || '';
                activeInput.val((val ? val + ' ' : '') + tag);
            }
            activeInput.focus();
        }
    });

    $('#templateEditForm').on('submit', function() {
        if (subtextEditor) {
            $('#inputSubtext').val(subtextEditor.getData());
        }
    });

    

    $('#categorySearchFilter').on('keyup', function() {
        let q = $(this).val().toLowerCase().trim();
        $('.category-row').each(function() {
            let catName = $(this).data('cat-name') || '';
            let parentName = $(this).data('parent-name') || '';
            if (catName.includes(q) || parentName.includes(q)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Open Configure Templates Modal
    $('.btn-open-configure').on('click', function(e) {
        e.preventDefault();
        $('#modalManageTemplates').modal('show');
    });

    $('.btn-add-option').on('click', function(e) {
        e.preventDefault();
        shouldReopenManageTemplates = true;
        let field = $(this).data('field');
        let section = $(this).data('section');
        let type = $(this).data('type') || 'combined';
        let f1 = $(this).data('f1') || 'Headline';
        let f2 = $(this).data('f2') || 'Subtext / Content';

        $('#templateEditForm').attr('action', '{{ route("admin.content-templates.store") }}');
        $('#tpl_id').val('');
        $('#tpl_section_group').val(section);
        $('#tpl_field_key').val(field);
        $('#inputHeadline').val('');
        $('#inputSubtext').val('');
        if (subtextEditor) {
            subtextEditor.setData('');
        }

        $('#lblField1').text(f1);
        $('#lblField2').text(f2);
        if (type === 'single') {
            $('#wrapSubtext').hide();
        } else {
            $('#wrapSubtext').show();
        }
        $('#tplModalTitle').text('Add New Option (' + f1 + ')');

        $('#modalManageTemplates').modal('hide');
        setTimeout(function() {
            $('#modalTemplateForm').modal('show');
            setTimeout(function() {
                $('#inputHeadline').focus();
            }, 300);
        }, 350);
    });

    function stripHtmlTags(str) {
        if (!str) return '';
        let tmp = document.createElement('div');
        tmp.innerHTML = str;
        return (tmp.textContent || tmp.innerText || '').trim();
    }

    function decodeHtmlEntities(str) {
        if (!str) return '';
        if (str.includes('&lt;') || str.includes('&gt;') || str.includes('&amp;')) {
            let txt = document.createElement('textarea');
            txt.innerHTML = str;
            let val = txt.value;
            if (val.includes('&lt;') || val.includes('&gt;')) {
                txt.innerHTML = val;
                val = txt.value;
            }
            val = val.replace(/^<p>\s*<p>/i, '<p>').replace(/<\/p>\s*<\/p>$/i, '</p>');
            return val;
        }
        return str;
    }

    $('.btn-edit-tpl').on('click', function(e) {
        e.preventDefault();
        shouldReopenManageTemplates = true;
        let id = $(this).data('id');
        let opt = $(this).data('option');
        let rawHeadline = $('#tpl_headline_' + id).val() || $(this).attr('data-headline') || $(this).data('headline') || '';
        let rawSubtext = $('#tpl_subtext_' + id).val() || $(this).attr('data-subtext') || $(this).data('subtext') || '';
        let headline = decodeHtmlEntities(rawHeadline);
        let subtext = decodeHtmlEntities(rawSubtext);
        let type = $(this).data('type') || 'combined';
        let f1 = $(this).data('f1') || 'Headline';
        let f2 = $(this).data('f2') || 'Subtext / Content';

        $('#templateEditForm').attr('action', '{{ route("admin.content-templates.update") }}');
        $('#tpl_id').val(id);
        $('#inputHeadline').val(headline);
        $('#inputSubtext').val(subtext);
        if (subtextEditor) {
            subtextEditor.setData(subtext);
        }

        $('#lblField1').text(f1);
        $('#lblField2').text(f2);
        if (type === 'single') {
            $('#wrapSubtext').hide();
        } else {
            $('#wrapSubtext').show();
        }
        $('#tplModalTitle').text('Edit Option #' + opt + ' (' + f1 + ')');

        $('#modalManageTemplates').modal('hide');
        setTimeout(function() {
            $('#modalTemplateForm').modal('show');
            setTimeout(function() {
                $('#inputHeadline').focus();
            }, 300);
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

    let currentModalCatId = null;
    $('.btn-view-assign').on('click', function(e) {
        e.preventDefault();
        currentModalCatId = $(this).data('cat-id');
        let catName = $(this).data('cat-name');
        let assignments = $(this).data('assignments') || {};

        $('#assignCatTitle').text(catName + ' (#' + currentModalCatId + ')');

        $('#assignTableBody tr').each(function() {
            let field = $(this).data('field');
            let select = $(this).find('select');
            if (assignments[field]) {
                select.val(assignments[field].template_id);
            } else {
                select.val('');
            }
        });

        $('#modalViewAssignments').modal('show');
    });

    // Reset Entity Assignments
    $('.btn-reset-entity').on('click', function(e) {
        e.preventDefault();
        let entityType = $(this).data('entity-type');
        let entityId = $(this).data('entity-id');
        let entityName = $(this).data('entity-name');

        let msg = 'Do you really want to reset the template assignments for "' + entityName + '"?\n\nThe assigned templates will be removed. You can then click "Assign Missing" at the top to re-assign randomly.';
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
                section_group: '{{ $sectionGroup }}',
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

    $('.btn-save-single-assign').on('click', function(e) {
        e.preventDefault();
        let field = $(this).data('field');
        let row = $(this).closest('tr');
        let templateId = row.find('select').val();

        if (!templateId) {
            alert('Please select a template option to assign.');
            return;
        }

        let btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: '{{ route("admin.content-templates.assign-single") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                entity_type: 'category',
                entity_id: currentModalCatId,
                field_key: field,
                template_id: templateId,
                lang_id: {{ $lang_id }}
            },
            success: function(res) {
                btn.prop('disabled', false).html('<em class="icon ni ni-check me-1"></em> Saved');
                setTimeout(() => {
                    btn.html('<em class="icon ni ni-check me-1"></em> Save Lock');
                    location.reload();
                }, 700);
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
