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
                Define criteria rating copy by category &amp; subcategory aligned with site content threshold ranges (e.g. 1.0-1.9, 2.0-2.9, ..., 4.5-5.0).
            </div>
        </div>
        <div class="nk-block-head-content d-flex align-items-center gap-2 mt-2 mt-md-0">
            <div class="d-flex align-items-center ms-2">
                <label class="form-label me-2 mb-0 font-weight-bold small text-nowrap">Language:</label>
                <select class="form-select form-select-sm" id="langSelect" style="min-width: 200px;" onchange="navigateCriteria(this.value, '{{ $activeCriteriaId }}', '{{ $activeCategoryId }}')">
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

    {{-- Filter Card: Criteria & Category Selection --}}
    <div class="card card-bordered mb-4 bg-light border-0 shadow-sm">
        <div class="card-inner py-4">
            <div class="row g-3 align-items-center">
                {{-- Select Criteria --}}
                <div class="col-md-5">
                    <label class="form-label font-weight-bold small text-muted mb-1 text-uppercase">
                        <em class="icon ni ni-star text-primary me-1"></em> Rating Criteria
                    </label>
                    <select class="form-select" id="criteriaSelect" onchange="navigateCriteria('{{ $lang_id }}', this.value, '{{ $activeCategoryId }}')">
                        @foreach($allCriteria as $criteria)
                            <option value="{{ $criteria->id }}" {{ $activeCriteriaId == $criteria->id ? 'selected' : '' }}>
                                {{ $criteria->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Select Category / Subcategory --}}
                <div class="col-md-7">
                    <label class="form-label font-weight-bold small text-muted mb-1 text-uppercase">
                        <em class="icon ni ni-folder text-primary me-1"></em> Category / Subcategory Scope
                    </label>
                    <select class="form-select" id="categorySelect" onchange="navigateCriteria('{{ $lang_id }}', '{{ $activeCriteriaId }}', this.value)">
                        <option value="0" {{ $activeCategoryId == 0 ? 'selected' : '' }}>
                            ★ Global Default (All Categories)
                        </option>
                        @foreach($categories as $parent)
                            @php
                                $pTrans = $parent->categoryTranslations->where('lang_id', $lang_id)->first() ?? $parent->categoryTranslations->first();
                                $parentName = $pTrans?->name ?? $parent->name ?? ('Category #' . $parent->id);
                            @endphp
                            <optgroup label="📁 {{ $parentName }}">
                                <option value="{{ $parent->id }}" {{ $activeCategoryId == $parent->id ? 'selected' : '' }}>
                                    📁 {{ $parentName }} (Entire Category)
                                </option>
                                @foreach($parent->subCategories as $sub)
                                    @php
                                        $sTrans = $sub->categoryTranslations->where('lang_id', $lang_id)->first() ?? $sub->categoryTranslations->first();
                                        $subName = $sTrans?->name ?? $sub->name ?? ('Subcategory #' . $sub->id);
                                    @endphp
                                    <option value="{{ $sub->id }}" {{ $activeCategoryId == $sub->id ? 'selected' : '' }}>
                                        &nbsp;&nbsp;&nbsp;&nbsp;↳ {{ $subName }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Inheritance Status Alert --}}
            @if($activeCategoryId > 0)
                <div class="mt-3">
                    @if($inheritanceInfo['has_custom_templates'])
                        <div class="alert alert-success d-flex align-items-center justify-content-between py-2 px-3 mb-0 small">
                            <div>
                                <em class="icon ni ni-check-circle-fill me-1 text-success"></em>
                                <strong>Custom Copy Active:</strong> 
                                @if($activeCategory?->parent_id)
                                    This subcategory (<strong>{{ $activeCategory->name }}</strong>) has its own overrides defined, which takes precedence over parent <strong>{{ $activeCategory->parent?->name }}</strong>.
                                @else
                                    This category (<strong>{{ $activeCategory->name }}</strong>) has its own custom templates defined.
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning d-flex flex-wrap align-items-center justify-content-between py-3 px-3 mb-0 small">
                            <div class="me-2 mb-2 mb-md-0">
                                <em class="icon ni ni-info-fill me-1 text-warning"></em>
                                <strong>Inherited Copy:</strong> 
                                Currently using templates from <strong>{{ $inheritanceInfo['source_label'] }}</strong> ({{ $inheritanceInfo['inherited_count'] }} variations). Adding an option below will customize copy for this {{ $activeCategory?->parent_id ? 'subcategory' : 'category' }}.
                            </div>
                            @if($inheritanceInfo['can_copy'])
                                <form action="{{ route('admin.content-templates.criteria-ratings.copy') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="from_section_group" value="{{ $inheritanceInfo['source_section_group'] }}">
                                    <input type="hidden" name="to_section_group" value="{{ $sectionGroup }}">
                                    <input type="hidden" name="lang_id" value="{{ $lang_id }}">
                                    <input type="hidden" name="criteria_id" value="{{ $activeCriteriaId }}">
                                    <input type="hidden" name="category_id" value="{{ $activeCategoryId }}">
                                    <button type="submit" class="btn btn-xs btn-primary">
                                        <em class="icon ni ni-copy me-1"></em> Copy All from {{ $inheritanceInfo['source_label'] }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
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
                        '[business]' => 'Provider Name',
                        '[year]' => date('Y'),
                        '[country1]' => 'United States',
                        '[country2]' => 'U.S.',
                    ] as $tag => $example)
                        <button type="button" class="btn btn-xs btn-primary btn-tag-insert py-0 px-2" data-tag="{{ $tag }}">{{ $tag }}</button>
                    @endforeach
                </div>
            </div>

            {{-- Field Selector Tabs (5 Defined Ranges) --}}
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
                                        <th style="width: 35%;">{{ $def['field1_label'] }}</th>
                                        @if(($def['type'] ?? '') !== 'single')
                                            <th>{{ $def['field2_label'] ?? 'Subtext / Content' }}</th>
                                        @endif
                                        <th class="text-end" style="width: 140px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($templates[$key] ?? [] as $item)
                                        @php
                                            $decodedHeadline = html_entity_decode($item->headline, ENT_QUOTES, 'UTF-8');
                                            $decodedSubtext = html_entity_decode($item->subtext, ENT_QUOTES, 'UTF-8');
                                            $cleanSnippet = Str::limit(strip_tags($decodedSubtext), 150);
                                        @endphp
                                        <tr>
                                            <td class="font-weight-bold text-center">
                                                <span class="badge bg-dark text-white">#{{ $item->option_number }}</span>
                                            </td>
                                            <td>
                                                <span class="text-dark fs-13px fw-bold">{{ $decodedHeadline }}</span>
                                            </td>
                                            @if(($def['type'] ?? '') !== 'single')
                                                <td>
                                                    <span class="text-muted small">{{ $cleanSnippet }}</span>
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
                                                <textarea id="tpl_headline_{{ $item->id }}" class="d-none">{{ $decodedHeadline }}</textarea>
                                                <textarea id="tpl_subtext_{{ $item->id }}" class="d-none">{!! $decodedSubtext !!}</textarea>
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
                                                No template variations defined yet for this range under this category. Click <strong>Add New Option</strong> above.
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

{{-- Add / Edit Template Modal --}}
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
                        <label class="form-label font-weight-bold small" id="lblField1">Upper text (Headline)</label>
                        <input type="text" name="headline" id="inputHeadline" class="form-control" placeholder="Enter headline / upper text...">
                    </div>
                    <div class="mb-3" id="wrapSubtext">
                        <label class="form-label font-weight-bold small" id="lblField2">Lower text (Rich Content)</label>
                        <textarea name="subtext" id="inputSubtext" class="form-control rich-editor" rows="5" placeholder="Enter rich lower text..."></textarea>
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
function navigateCriteria(langId, criteriaId, categoryId) {
    let url = '{{ route("admin.content-templates.criteria-ratings") }}?lang_id=' + (langId || 1) 
        + '&criteria_id=' + (criteriaId || 1) 
        + '&category_id=' + (categoryId || 0);
    window.location.href = url;
}

$(document).ready(function() {
    // Move modals directly to <body> so they are never trapped in stacking contexts
    $('#modalTemplateForm').appendTo('body');

    // Universal explicit handler for all modal close buttons
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

    $('.btn-add-option').on('click', function(e) {
        e.preventDefault();
        let field = $(this).data('field');
        let section = $(this).data('section');
        let type = $(this).data('type') || 'combined';
        let f1 = $(this).data('f1') || 'Upper text';
        let f2 = $(this).data('f2') || 'Lower text';

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
        let rawHeadline = $('#tpl_headline_' + id).val() || $(this).attr('data-headline') || $(this).data('headline') || '';
        let rawSubtext = $('#tpl_subtext_' + id).val() || $(this).attr('data-subtext') || $(this).data('subtext') || '';
        let headline = decodeHtmlEntities(rawHeadline);
        let subtext = decodeHtmlEntities(rawSubtext);
        let type = $(this).data('type') || 'combined';
        let f1 = $(this).data('f1') || 'Upper text';
        let f2 = $(this).data('f2') || 'Lower text';

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
        $('#tplModalTitle').text('Edit Option #' + opt);

        $('#modalTemplateForm').modal('show');
        setTimeout(function() { $('#inputHeadline').focus(); }, 300);
    });
});
</script>
@endpush
@endsection
