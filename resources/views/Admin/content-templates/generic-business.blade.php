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
                <span><em class="icon ni ni-building text-primary me-2"></em>Content Templates &mdash; {{ $title }}</span>
                @if(isset($selectedLanguage))
                    <span class="badge bg-primary ms-2 font-weight-normal fs-12px">{{ $selectedLanguage->name }} ({{ $selectedLanguage->lang_code }})</span>
                @endif
            </h4>
            <div class="text-muted small">
                Define randomized template variations for {{ $title }} headlines and content. Once assigned to a business, options are <strong>permanently locked</strong> in the database.
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
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <em class="icon ni ni-alert-circle me-1"></em> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" data-bs-dismiss="alert" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </button>
        </div>
    @endif

    {{-- Stats Card --}}
    <div class="card card-bordered mb-4 bg-light border-0 shadow-sm">
        <div class="card-inner py-3">
            <div class="d-flex flex-wrap align-items-center small">
                @foreach($fieldDefs as $fKey => $fDef)
                    <div class="{{ $loop->first ? '' : 'border-start ps-3' }}">
                        <span class="text-muted d-block">{{ $fDef['label'] }}:</span>
                        <strong class="text-dark filter-count-{{ $fKey }}">{{ $templates->get($fKey)?->count() ?? 0 }} options</strong>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card card-bordered mb-4 bg-light border-0 shadow-sm">
        <div class="card-inner py-3">
            <form method="GET" action="{{ url()->current() }}" class="row g-3 align-items-end">
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
                    <label class="form-label mb-1 small fw-bold">Search Business Name</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="e.g. Hostinger, Bluehost, Slack...">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
                    <a href="{{ url()->current() }}?lang_id={{ $lang_id }}" class="btn btn-sm btn-light">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Content Table --}}
    <div class="card card-bordered">
        <div class="card-inner p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="businessTemplatesTable">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th style="min-width: 240px;">Business Name</th>
                            <th style="min-width: 180px;">Category</th>
                            @foreach($fieldDefs as $fDef)
                                <th class="text-center" style="min-width: 100px;">{{ $fDef['label'] }}</th>
                            @endforeach
                            <th class="text-end pe-3" style="min-width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($businesses as $biz)
                            @php
                                $bizTrans = $biz->translations->where('lang_id', $lang_id)->first() ?? $biz->translations->first();
                                $bizName = $bizTrans?->name ?? ('Business #' . $biz->id);

                                $catTrans = $biz->category?->categoryTranslations->where('lang_id', $lang_id)->first() ?? $biz->category?->categoryTranslations->first();
                                $catName = $catTrans?->name ?? $biz->category?->name ?? 'N/A';

                                $subNames = $biz->subCategories->map(function($sub) use ($lang_id) {
                                    $st = $sub->categoryTranslations->where('lang_id', $lang_id)->first() ?? $sub->categoryTranslations->first();
                                    return $st?->name ?? $sub->name;
                                })->filter()->implode(', ');

                                $bizAssignments = $assignments->get($biz->id, collect())->keyBy('field_key');
                            @endphp
                            <tr class="business-row bg-white">
                                <td class="py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar bg-light border text-primary sq me-2" style="width: 32px; height: 32px; font-size: 13px; font-weight: 700;">
                                            {{ strtoupper(substr($bizName, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="fs-14px fw-bold text-dark d-block">{{ $bizName }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-secondary text-white">{{ $catName }}</span>
                                    @if($subNames)
                                        <div class="text-muted small mt-1" style="font-size: 11px;">
                                            <em class="icon ni ni-chevron-right me-1"></em>{{ Str::limit($subNames, 40) }}
                                        </div>
                                    @endif
                                </td>
                                @foreach(array_keys($fieldDefs) as $fKey)
                                    @php
                                        $asgn = $bizAssignments->get($fKey);
                                        $tpl = $asgn?->template;
                                    @endphp
                                    <td class="text-center py-3">
                                        @if($tpl)
                                            <span class="fw-bold text-primary cursor-pointer fs-13px btn-cell-assign badge bg-light text-primary border"
                                                  data-toggle="tooltip" data-bs-toggle="tooltip"
                                                  title="Option #{{ $tpl->option_number }}: {{ $tpl->headline ? Str::limit(strip_tags(html_entity_decode($tpl->headline)), 60) : '' }} (Click to edit)">
                                                #{{ $tpl->option_number }}
                                            </span>
                                        @else
                                            <span class="text-muted fs-13px btn-cell-assign cursor-pointer" title="Click to assign">-</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-end pe-3 py-3">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <button type="button" class="btn btn-sm btn-dark btn-view-assign p-1 px-2"
                                                data-biz-id="{{ $biz->id }}"
                                                data-biz-name="{{ $bizName }}"
                                                data-cat-name="{{ $catName }}"
                                                data-assignments="{{ json_encode($bizAssignments->map(fn($a) => ['template_id' => $a->template_id, 'option_number' => $a->template?->option_number])) }}"
                                                title="Edit Assignments" data-toggle="tooltip">
                                            <em class="icon ni ni-edit"></em>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger btn-reset-entity p-1 px-2 ms-1"
                                                data-entity-type="business"
                                                data-entity-id="{{ $biz->id }}"
                                                data-entity-name="{{ $bizName }}"
                                                title="Reset Assignments" data-toggle="tooltip">
                                            <em class="icon ni ni-reload"></em>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($fieldDefs) + 3 }}" class="text-center py-5 text-muted">
                                    <div class="mb-2"><em class="icon ni ni-building fs-36px text-light"></em></div>
                                    <h6 class="text-dark mb-1">No Businesses Found</h6>
                                    <p class="small text-muted mb-0">Try clearing the search or category filter to see all businesses.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($businesses->hasPages())
                <div class="card-inner py-3 border-top d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted small">
                        Showing <strong>{{ $businesses->firstItem() }}</strong> to <strong>{{ $businesses->lastItem() }}</strong> of <strong>{{ $businesses->total() }}</strong> businesses
                    </div>
                    <div>
                        {{ $businesses->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @endif
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
                        @foreach([
                            '[business]' => 'FlowSync Tech',
                            '[business_cap]' => 'FlowSync Tech',
                            '[business_lc]' => 'flowsync tech',
                            '[category_cap]' => 'Web hosting',
                            '[category_tc]' => 'Web Hosting',
                            '[category_lc]' => 'web hosting',
                            '[subcategory_cap]' => 'Cloud hosting',
                            '[subcategory_tc]' => 'Cloud Hosting',
                            '[subcategory_lc]' => 'cloud hosting',
                            '[entity1_cap]' => 'Web hosting provider',
                            '[entity1_tc]' => 'Web Hosting Provider',
                            '[entity1_lc]' => 'web hosting provider',
                            '[entities1_cap]' => 'Web hosting providers',
                            '[entities1_lc]' => 'web hosting providers',
                            '[count]' => '3',
                            '[rating]' => '4.8',
                            '[reviews]' => '150',
                            '[year]' => date('Y'),
                            '[country1]' => 'United States',
                            '[country2]' => 'U.S.',
                        ] as $tag => $example)
                            <button type="button" class="btn btn-xs btn-white border btn-tag-insert" data-tag="{{ $tag }}" title="Example: {{ $example }}">
                                <code>{{ $tag }}</code>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Nav Tabs for Template Field Categories --}}
                <ul class="nav nav-tabs nav-tabs-mb-icon nav-tabs-card">
                    @foreach($fieldDefs as $key => $def)
                        <li class="nav-item">
                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-toggle="tab" data-bs-toggle="tab" href="#tab-tpl-{{ $key }}">
                                <span>{{ $def['label'] }}</span>
                                <span class="badge rounded-pill bg-light text-dark ms-1">{{ $templates[$key]?->count() ?? 0 }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content mt-3">
                    @foreach($fieldDefs as $key => $def)
                        <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="tab-tpl-{{ $key }}">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-0 text-dark">{{ $def['label'] }} Templates</h6>
                                    <span class="text-muted small">Manage headline and content variations for this section.</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary btn-add-option" 
                                        data-field="{{ $key }}" 
                                        data-section="{{ $sectionGroup }}"
                                        data-type="{{ $def['type'] }}"
                                        data-f1="{{ $def['field1_label'] }}"
                                        data-f2="{{ $def['field2_label'] ?? '' }}">
                                    <em class="icon ni ni-plus me-1"></em> Add New Option
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead class="table-light small">
                                        <tr>
                                            <th style="width: 80px;" class="text-center">Option #</th>
                                            @if(($def['type'] ?? '') === 'rich')
                                                <th>{{ $def['field1_label'] }}</th>
                                            @elseif(($def['type'] ?? '') !== 'single')
                                                <th>{{ $def['field1_label'] }}</th>
                                                <th>{{ $def['field2_label'] ?? 'Subtext / Content' }}</th>
                                            @else
                                                <th>{{ $def['field1_label'] }}</th>
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
                                                @if(($def['type'] ?? '') === 'rich')
                                                    <td>
                                                        <span class="text-dark small">{{ Str::limit(strip_tags(html_entity_decode($item->subtext ?: $item->headline)), 150) }}</span>
                                                    </td>
                                                @elseif(($def['type'] ?? '') !== 'single')
                                                    <td>
                                                        <span class="text-dark small">{{ Str::limit(strip_tags(html_entity_decode($item->headline)), 150) }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-muted small">{{ Str::limit(strip_tags(html_entity_decode($item->subtext)), 150) }}</span>
                                                    </td>
                                                @else
                                                    <td>
                                                        <span class="text-dark small">{{ Str::limit(strip_tags(html_entity_decode($item->headline)), 150) }}</span>
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
            <div class="modal-footer bg-light py-2 d-flex justify-content-between">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-success" id="btnSaveAllAssignments">
                    <em class="icon ni ni-check-circle me-1"></em> Save All Locks
                </button>
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
                    <div class="mb-3" id="wrapHeadline">
                        <label class="form-label font-weight-bold small" id="lblField1">Headline</label>
                        <input type="text" name="headline" id="inputHeadline" class="form-control" placeholder="Enter headline template...">
                    </div>
                    <div class="mb-3" id="wrapSubtext">
                        <label class="form-label font-weight-bold small" id="lblField2">Subtext / Content</label>
                        <textarea name="subtext" id="inputSubtext" class="form-control rich-editor" rows="5" placeholder="Enter subtext or rich HTML template..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold small">Assign immediately to business (Optional)</label>
                        <select name="assign_to_business_id" id="inputAssignToBusiness" class="form-select form-select-sm">
                            <option value="">-- Do not assign now (just save template) --</option>
                            <option value="all">Assign to All Businesses</option>
                            @foreach($businesses as $b)
                                @php
                                    $bTrans = $b->translations->where('lang_id', $lang_id)->first() ?? $b->translations->first();
                                    $bName = $bTrans?->name ?? ('Business #' . $b->id);
                                @endphp
                                <option value="{{ $b->id }}">Assign to {{ $bName }}</option>
                            @endforeach
                        </select>
                        <div class="text-muted small mt-1">If selected, this option will be immediately locked to the chosen business so you can test it on the front-end right away!</div>
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

{{-- Modal: View / Override Business Assignments --}}
<div class="modal fade" id="modalViewAssignments" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light py-3">
                <h5 class="modal-title d-flex align-items-center">
                    <em class="icon ni ni-building text-primary me-2"></em> Locked Template Assignments &mdash; <span id="assignBizTitle" class="ms-1"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">
                    Below are the currently locked template options for this business. You can re-assign or select a different option and save to update the lock.
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light small">
                            <tr>
                                <th style="width: 35%;">Section</th>
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
                                                    Option #{{ $tOption->option_number }} &bull; {{ Str::limit(strip_tags(html_entity_decode($tOption->headline)), 60) }}
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
            <div class="modal-footer bg-light py-2 d-flex justify-content-between">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-success" id="btnSaveAllAssignments">
                    <em class="icon ni ni-check-circle me-1"></em> Save All Locks
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Move modals directly to <body> so they are never trapped in .nk-content / .nk-block stacking contexts
    $('#modalManageTemplates, #modalTemplateForm, #modalViewAssignments').appendTo('body');

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
        let targetTabBtn = $('a[href="#tab-tpl-{{ session("open_manage_field") }}"]');
        if (targetTabBtn.length) {
            targetTabBtn.tab('show');
        }
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

                        let tabBadge = $('a[href="#tab-tpl-' + fieldKey + '"] .badge');
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
            let sData = subtextEditor.getData();
            $('#inputSubtext').val(sData);
            if (!$('#wrapHeadline').is(':visible') || $('#tpl_field_key').val() === 'alternatives_subtext') {
                $('#inputHeadline').val(stripHtmlTags(sData));
            }
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

        if (type === 'single') {
            $('#wrapHeadline').show();
            $('#wrapSubtext').hide();
            $('#lblField1').text(f1);
            activeInput = $('#inputHeadline');
        } else if (type === 'rich') {
            $('#wrapHeadline').hide();
            $('#wrapSubtext').show();
            $('#lblField2').text(f1 || 'Sub-headline');
            activeInput = $('#inputSubtext');
        } else {
            $('#wrapHeadline').show();
            $('#wrapSubtext').show();
            $('#lblField1').text(f1);
            $('#lblField2').text(f2);
            activeInput = $('#inputHeadline');
        }
        $('#tplModalTitle').text('Add New Option (' + f1 + ')');

        $('#modalManageTemplates').modal('hide');
        setTimeout(function() {
            $('#modalTemplateForm').modal('show');
            setTimeout(function() {
                if (type === 'rich' && subtextEditor) {
                    subtextEditor.editing.view.focus();
                    activeInput = $('#inputSubtext');
                } else {
                    $('#inputHeadline').focus();
                }
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
        let headline = stripHtmlTags(decodeHtmlEntities(rawHeadline));
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

        if (type === 'single') {
            $('#wrapHeadline').show();
            $('#wrapSubtext').hide();
            $('#lblField1').text(f1);
            activeInput = $('#inputHeadline');
        } else if (type === 'rich') {
            let richVal = subtext || rawHeadline;
            $('#inputSubtext').val(richVal);
            if (subtextEditor) {
                subtextEditor.setData(richVal);
            }
            $('#wrapHeadline').hide();
            $('#wrapSubtext').show();
            $('#lblField2').text(f1 || 'Sub-headline');
            activeInput = $('#inputSubtext');
        } else {
            $('#wrapHeadline').show();
            $('#wrapSubtext').show();
            $('#lblField1').text(f1);
            $('#lblField2').text(f2);
            activeInput = $('#inputHeadline');
        }
        $('#tplModalTitle').text('Edit Option #' + opt + ' (' + f1 + ')');

        $('#modalManageTemplates').modal('hide');
        setTimeout(function() {
            $('#modalTemplateForm').modal('show');
            setTimeout(function() {
                if (type === 'rich' && subtextEditor) {
                    subtextEditor.editing.view.focus();
                    activeInput = $('#inputSubtext');
                } else {
                    $('#inputHeadline').focus();
                }
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

    let currentModalBizId = null;
    $('.btn-view-assign').on('click', function(e) {
        e.preventDefault();
        currentModalBizId = $(this).data('biz-id');
        let bizName = $(this).data('biz-name');
        let catName = $(this).data('cat-name');
        let assignments = $(this).data('assignments') || {};

        $('#assignBizTitle').text(bizName + ' (' + catName + ')');

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

    // Save single assignment from modal
    $('.btn-save-single-assign').on('click', function(e) {
        e.preventDefault();
        let field = $(this).data('field');
        let row = $(this).closest('tr');
        let select = row.find('select');
        let templateId = select.val();

        if (!templateId) {
            alert('Please select a template option before saving.');
            return;
        }

        let btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: '{{ route("admin.content-templates.assign-single") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                entity_type: 'business',
                entity_id: currentModalBizId,
                field_key: field,
                template_id: templateId,
                lang_id: '{{ $lang_id }}'
            },
            success: function(resp) {
                btn.prop('disabled', false).html('<em class="icon ni ni-check me-1"></em> Saved');
                setTimeout(() => {
                    btn.html('<em class="icon ni ni-check me-1"></em> Save Lock');
                    location.reload();
                }, 700);
            },
            error: function() {
                btn.prop('disabled', false).html('<em class="icon ni ni-check me-1"></em> Save Lock');
                alert('Failed to save assignment. Please try again.');
            }
        });
    });

    // Save all assignments from modal at once
    $('#btnSaveAllAssignments').on('click', function(e) {
        e.preventDefault();
        let btn = $(this);
        let rows = $('#assignTableBody tr');
        let calls = [];

        rows.each(function() {
            let field = $(this).data('field');
            let select = $(this).find('select');
            let templateId = select.val();
            if (templateId) {
                calls.push({ field: field, template_id: templateId });
            }
        });

        if (calls.length === 0) {
            alert('No template options selected to save.');
            return;
        }

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving all...');

        let promises = calls.map(function(item) {
            return $.ajax({
                url: '{{ route("admin.content-templates.assign-single") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    entity_type: 'business',
                    entity_id: currentModalBizId,
                    field_key: item.field,
                    template_id: item.template_id,
                    lang_id: '{{ $lang_id }}'
                }
            });
        });

        $.when.apply($, promises).then(function() {
            btn.html('<em class="icon ni ni-check-circle me-1"></em> All Saved!');
            setTimeout(function() {
                location.reload();
            }, 700);
        }).fail(function() {
            btn.prop('disabled', false).html('<em class="icon ni ni-check-circle me-1"></em> Save All Locks');
            alert('Failed to save some assignments. Please try again.');
        });
    });

    // Clicking an assigned option number in the table opens the assignment modal for that business
    $(document).on('click', '.btn-cell-assign', function(e) {
        e.preventDefault();
        let row = $(this).closest('tr');
        row.find('.btn-view-assign').trigger('click');
    });
});
</script>
@endpush
@endsection
