@extends('admin_layout.master')
@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between align-items-center">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Feature Content Templates</h3>
                    <div class="nk-block-des text-soft">
                        <p>Manage randomized content templates across all sections for feature landing pages. Define distinct templates for H1 headlines, intro texts, top-rated listings, comparisons, and tools.</p>
                    </div>
                </div>
                <div class="nk-block-head-content" style="width: -webkit-fill-available; display: flex; justify-content: flex-end;">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <ul class="nk-block-tools g-3">
                            <li>
                                <form action="{{ route('admin.content-templates.assign-missing') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="section" value="features">
                                    <input type="hidden" name="lang_id" value="{{ $lang_id }}">
                                    <button type="submit" class="btn btn-primary">
                                        <em class="icon ni ni-shuffle me-1"></em> Assign Missing (Random & Lock)
                                    </button>
                                </form>
                            </li>
                            <li>
                                <button type="button" class="btn btn-dark btn-open-configure" data-toggle="modal" data-target="#modalManageTemplates" data-bs-toggle="modal" data-bs-target="#modalManageTemplates">
                                    <em class="icon ni ni-setting me-1"></em> Configure Templates
                                </button>
                            </li>
                            <li class="d-flex align-items-center">
                                <label class="form-label me-2 mb-0 font-weight-bold small text-nowrap">Language:</label>
                                <select class="form-select form-select-sm" id="langSelect" style="min-width: 170px;" onchange="window.location.href='{{ route('admin.content-templates.features') }}?lang_id=' + this.value">
                                    @if(isset($languages))
                                        @foreach ($languages as $lang)
                                            <option value="{{ $lang->id }}" {{ (isset($lang_id) && $lang_id == $lang->id) ? 'selected' : '' }}>
                                                {{ $lang->name }} ({{ $lang->lang_code }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </li>
                        </ul>
                    </div>
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

        {{-- Summary Stats Card --}}
        <div class="card card-bordered mb-4 bg-light border-0 shadow-sm">
            <div class="card-inner py-3">
                <div class="d-flex flex-wrap align-items-center small">
                    @foreach($fieldDefs as $fKey => $fDef)
                        <div class="{{ $loop->first ? '' : 'border-start ps-3' }}">
                            <span class="text-muted d-block">{{ $fDef['label'] }}:</span>
                            <strong class="text-dark">{{ $templates->get($fKey)?->count() ?? 0 }} options</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

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
                                <th style="min-width: 220px;">Feature Name</th>
                                <th style="min-width: 150px;">Category</th>
                                @foreach($fieldDefs as $fDef)
                                    <th class="text-center" style="min-width: 95px;">{{ $fDef['label'] }}</th>
                                @endforeach
                                <th class="text-end pe-3" style="min-width: 95px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($features as $feature)
                                @php
                                    $fTrans = $feature->translations->where('lang_id', $lang_id)->first() ?? $feature->translations->first();
                                    $featureName = $fTrans?->name ?? $feature->name ?? ('Feature #' . $feature->id);
                                    
                                    $catTrans = $feature->category?->categoryTranslations->where('lang_id', $lang_id)->first() ?? $feature->category?->categoryTranslations->first();
                                    $categoryName = $catTrans?->name ?? $feature->category?->name ?? 'Category';

                                    $featAssignments = $assignments->get($feature->id, collect())->keyBy('field_key');
                                @endphp
                                <tr class="feature-row bg-white">
                                    <td class="py-3">
                                        <strong class="fs-14px text-dark d-block">{{ $featureName }}</strong>
                                        <span class="text-muted small">ID: {{ $feature->id }}</span>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge bg-primary text-white">{{ $categoryName }}</span>
                                    </td>
                                    @foreach(array_keys($fieldDefs) as $fKey)
                                        @php
                                            $asgn = $featAssignments->get($fKey);
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
                                                    data-feat-id="{{ $feature->id }}"
                                                    data-feat-name="{{ $featureName }}"
                                                    data-cat-name="{{ $categoryName }}"
                                                    data-assignments="{{ json_encode($featAssignments->map(fn($a) => ['template_id' => $a->template_id, 'option_number' => $a->template?->option_number])) }}"
                                                    title="Edit Assignments" data-toggle="tooltip">
                                                <em class="icon ni ni-edit"></em>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger btn-reset-entity p-1 px-2 ms-1"
                                                    data-entity-type="feature"
                                                    data-entity-id="{{ $feature->id }}"
                                                    data-entity-name="{{ $featureName }}"
                                                    title="Reset Assignments" data-toggle="tooltip">
                                                <em class="icon ni ni-reload"></em>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center py-4 text-muted">
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
</div>

{{-- Modal: Configure Templates --}}
<div class="modal fade" id="modalManageTemplates" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light py-3">
                <h5 class="modal-title d-flex align-items-center">
                    <em class="icon ni ni-setting text-primary me-2"></em> Configure Feature Content Templates
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
                            '[category_tc]' => 'Web Hosting',
                            '[category_cap]' => 'Web hosting',
                            '[category_lc]' => 'web hosting',
                            '[feature_tc]' => 'Free Domain Privacy',
                            '[feature_cap]' => 'Free domain privacy',
                            '[feature_lc]' => 'free domain privacy',
                            '[entity1_cap]' => 'Web hosting provider',
                            '[entity1_lc]' => 'web hosting provider',
                            '[entities1_cap]' => 'Web hosting providers',
                            '[entities1_lc]' => 'web hosting providers',
                            '[year]' => date('Y'),
                            '[country1]' => 'United States',
                            '[country2]' => 'U.S.',
                        ] as $tag => $example)
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
                                        data-section="features" 
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
                                            @if($def['type'] === 'combined')
                                                <th>{{ $def['field2_label'] }}</th>
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
                                                @if($def['type'] === 'combined')
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
                                                <td colspan="{{ $def['type'] === 'combined' ? 4 : 3 }}" class="text-center py-4 text-muted">
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
                <input type="hidden" name="section_group" id="tpl_section_group" value="features">
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
                        <label class="form-label font-weight-bold small" id="lblField1">Headline / Question</label>
                        <input type="text" name="headline" id="inputHeadline" class="form-control" placeholder="Enter headline template...">
                    </div>
                    <div class="mb-3" id="wrapSubtext">
                        <label class="form-label font-weight-bold small" id="lblField2">Subtext / Content</label>
                        <textarea name="subtext" id="inputSubtext" class="form-control rich-editor" rows="4" placeholder="Enter subtext template..."></textarea>
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

{{-- Modal: View / Override Feature Assignments --}}
<div class="modal fade" id="modalViewAssignments" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light py-3">
                <h5 class="modal-title d-flex align-items-center">
                    <em class="icon ni ni-tag text-primary me-2"></em> Locked Template Assignments &mdash; <span id="assignFeatTitle" class="ms-1"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">
                    Below are the currently locked template options for this feature. You can re-assign or select a different option and save to update the lock.
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
                                            @foreach($templates->get($fKey) ?? [] as $tOption)
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

    let currentModalFeatId = null;
    $('.btn-view-assign').on('click', function(e) {
        e.preventDefault();
        currentModalFeatId = $(this).data('feat-id');
        let featName = $(this).data('feat-name');
        let catName = $(this).data('cat-name');
        let assignments = $(this).data('assignments') || {};

        $('#assignFeatTitle').text(featName + ' (' + catName + ')');

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
                entity_type: 'feature',
                entity_id: currentModalFeatId,
                field_key: field,
                template_id: templateId,
                lang_id: '{{ $lang_id }}'
            },
            success: function(resp) {
                btn.prop('disabled', false).html('<em class="icon ni ni-check me-1"></em> Saved');
                setTimeout(() => {
                    btn.html('<em class="icon ni ni-check me-1"></em> Save Lock');
                }, 1500);
            },
            error: function() {
                btn.prop('disabled', false).html('<em class="icon ni ni-check me-1"></em> Save Lock');
                alert('Failed to save assignment. Please try again.');
            }
        });
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
});
</script>
@endpush
@endsection
