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
                <span><em class="icon ni ni-star-fill text-primary me-2"></em>Content Templates &mdash; Criteria Ratings</span>
                @if(isset($selectedLanguage))
                    <span class="badge bg-primary ms-2 font-weight-normal fs-12px">{{ $selectedLanguage->name }} ({{ $selectedLanguage->lang_code }})</span>
                @endif
            </h4>
            <div class="text-muted small">
                Define dynamic templates for criteria ratings based on ranges (e.g. 1.0-1.9, 2.0-2.9, etc.). 
            </div>
        </div>
        <div class="nk-block-head-content d-flex align-items-center gap-2 mt-2 mt-md-0">
            <div class="d-flex align-items-center ms-2">
                <label class="form-label me-2 mb-0 font-weight-bold small text-nowrap">Language:</label>
                <select class="form-select form-select-sm" id="langSelect" style="min-width: 200px;" onchange="window.location.href='{{ route('admin.content-templates.criteria-ratings') }}?criteria_id={{ $activeCriteriaId }}&lang_id=' + this.value">
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

    <div class="card card-bordered mb-4">
        <div class="card-inner">
            <h5 class="card-title">Select Criteria</h5>
            <p class="text-muted small">Select the rating criteria to define templates for its ranges.</p>
            <div class="form-group" style="max-width: 400px;">
                <select class="form-select" onchange="window.location.href='{{ route('admin.content-templates.criteria-ratings') }}?lang_id={{ $lang_id }}&criteria_id=' + this.value">
                    @foreach($allCriteria as $criteria)
                        <option value="{{ $criteria->id }}" {{ $activeCriteriaId == $criteria->id ? 'selected' : '' }}>
                            {{ $criteria->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @if($activeCriteriaId)
    <div class="card card-bordered">
        <div class="card-inner p-4">
            
            {{-- Supported Variables Bar --}}
            <div class="alert alert-info py-3 px-3 mb-4 small">
                <strong>Supported Variables:</strong> Click any tag to insert into the active input:
                <div class="d-flex flex-wrap gap-1 mt-2">
                    @foreach([
                        '[category_cap]' => 'Web hosting',
                        '[category_tc]' => 'Web Hosting',
                        '[category_lc]' => 'web hosting',
                        '[feature_cap]' => 'Feature Name',
                        '[feature_tc]' => 'Feature Name',
                        '[feature_lc]' => 'feature name',
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
                                                <span class="font-monospace text-dark small">{{ $item->headline }}</span>
                                            </td>
                                            @if(($def['type'] ?? '') !== 'single')
                                                <td>
                                                    <span class="font-monospace text-muted small">{{ Str::limit(strip_tags($item->subtext), 140) }}</span>
                                                </td>
                                            @endif
                                            <td class="text-end">
                                                <button type="button" class="btn btn-xs btn-primary btn-edit-tpl me-1" 
                                                        data-id="{{ $item->id }}" 
                                                        data-option="{{ $item->option_number }}"
                                                        data-headline="{{ htmlspecialchars($item->headline) }}" 
                                                        data-subtext="{{ htmlspecialchars($item->subtext) }}"
                                                        data-type="{{ $def['type'] }}"
                                                        data-f1="{{ $def['field1_label'] }}"
                                                        data-f2="{{ $def['field2_label'] ?? '' }}">
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
    </div>
    @endif
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
                        <input type="text" name="headline" id="inputHeadline" class="form-control" placeholder="Enter template...">
                    </div>
                    <div class="mb-3" id="wrapSubtext">
                        <label class="form-label font-weight-bold small" id="lblField2">Subtext / Content</label>
                        <textarea name="subtext" id="inputSubtext" class="form-control rich-editor" rows="5" placeholder="Enter rich HTML template..."></textarea>
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

@push('scripts')
<script>
$(document).ready(function() {
    // Move modals directly to <body> so they are never trapped in .nk-content / .nk-block stacking contexts
    $('#modalTemplateForm').appendTo('body');

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

    // Initialize CKEditor
    initSubtextEditor();

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
            let val = activeInput.val() || '';
            activeInput.val((val ? val + ' ' : '') + tag);
            activeInput.focus();
        }
    });

    $('#templateEditForm').on('submit', function() {
        if (subtextEditor) {
            $('#inputSubtext').val(subtextEditor.getData());
        }
    });

    $('.btn-add-option').on('click', function(e) {
        e.preventDefault();
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

        $('#modalTemplateForm').modal('show');
        setTimeout(function() { $('#inputHeadline').focus(); }, 300);
    });

    $('.btn-edit-tpl').on('click', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let opt = $(this).data('option');
        let headline = $(this).data('headline') || '';
        let subtext = $(this).data('subtext') || '';
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

        $('#modalTemplateForm').modal('show');
        setTimeout(function() { $('#inputHeadline').focus(); }, 300);
    });
});
</script>
@endpush
@endsection
