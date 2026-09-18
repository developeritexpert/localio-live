@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg">
    {{-- Header Banner --}}
    <div class="nk-block-head d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title mb-1 d-flex align-items-center flex-wrap">
                <span><em class="icon ni ni-building text-primary me-2"></em>Meta Tags &mdash; Businesses</span>
                @if(isset($selectedLanguage))
                    <span class="badge bg-primary ms-2 font-weight-normal fs-12px">{{ $selectedLanguage->name }} ({{ $selectedLanguage->lang_code }})</span>
                @endif
            </h4>
            <div class="text-muted small">
                Manage dynamic 3-tier cascade defaults (Primary, Secondary, Tertiary) based on character limits, or define custom manual overrides for any business profile.
            </div>
        </div>
        <div class="nk-block-head-content d-flex align-items-center gap-2 mt-2 mt-md-0">
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalDefaultRules">
                <em class="icon ni ni-setting me-1"></em> Configure Defaults & Limits
            </button>
            <div class="d-flex align-items-center ms-2">
                <label class="form-label me-2 mb-0 font-weight-bold small text-nowrap">Language:</label>
                <select class="form-select form-select-sm" id="langSelect" style="min-width: 220px;" onchange="window.location.href='{{ route('admin.meta-tags.business-discussions') }}?lang_id=' + this.value">
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

    {{-- Rules Overview Card --}}
    <div class="card card-bordered mb-4 bg-light border-0 shadow-sm">
        <div class="card-inner py-3">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex flex-wrap align-items-center">
                        <div>
                            <span class="text-muted small d-block">Title Limit:</span>
                            <strong class="text-primary fs-14px">{{ $rule->title_char_limit }} characters</strong>
                        </div>
                        <div class="border-start ps-3">
                            <span class="text-muted small d-block">Title Cascade Rule ({{ $selectedLanguage->lang_code ?? 'default' }}):</span>
                            <span class="small font-monospace text-dark">
                                1&deg;: {{ $rule->title_primary }} 
                                <em class="icon ni ni-arrow-right text-muted mx-1"></em>
                                2&deg;: {{ $rule->title_secondary ?? 'None' }}
                                <em class="icon ni ni-arrow-right text-muted mx-1"></em>
                                3&deg;: {{ $rule->title_tertiary ?? 'None' }}
                                <em class="icon ni ni-arrow-right text-muted mx-1"></em>
                                4&deg;: {{ $rule->title_quaternary ?? 'None' }}
                            </span>
                        </div>
                        <div class="border-start ps-3">
                            <span class="text-muted small d-block">Description Limit:</span>
                            <strong class="text-primary fs-14px">{{ $rule->description_char_limit }} characters</strong>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end mt-2 mt-lg-0">
                    <div class="form-control-wrap">
                        <div class="form-icon form-icon-left">
                            <em class="icon ni ni-search"></em>
                        </div>
                        <input type="text" id="businessSearchFilter" class="form-control form-control-sm" placeholder="Filter businesses by name...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form for Bulk Saving --}}
    <form action="{{ route('admin.meta-tags.business-discussions.update-bulk') }}" method="POST" id="bulkMetaForm">
        @csrf
        <input type="hidden" name="lang_id" value="{{ $lang_id }}">

        <div class="card card-bordered">
            <div class="card-inner p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="businessesTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 25%;">Business Profile</th>
                                <th style="width: 32%;">
                                    Title Tag
                                    <span class="badge bg-outline-secondary ms-1 fs-11px font-weight-normal">Max {{ $rule->title_char_limit }} chars</span>
                                </th>
                                <th style="width: 33%;">
                                    Description Tag
                                    <span class="badge bg-outline-secondary ms-1 fs-11px font-weight-normal">Max {{ $rule->description_char_limit }} chars</span>
                                </th>
                                <th class="text-end pe-3" style="width: 10%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($businesses as $business)
                                <tr class="business-row bg-white" data-biz-id="{{ $business->id }}" data-biz-name="{{ strtolower($business->metaData['name']) }}" data-cat-name="{{ strtolower($business->metaData['category_name']) }}">
                                    <td class="align-top py-3">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <strong class="fs-15px text-dark d-block">{{ $business->metaData['name'] }}</strong>
                                                <span class="text-muted small">
                                                    #{{ $business->id }} 
                                                    @if(!empty($business->metaData['category_name']))
                                                        &bull; {{ $business->metaData['category_name'] }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Title Tag Input --}}
                                    <td class="align-top py-3">
                                        <div class="meta-field-wrap" data-limit="{{ $rule->title_char_limit }}">
                                            <div class="input-group input-group-sm mb-1">
                                                <input type="text" 
                                                       name="meta_title[{{ $business->id }}]" 
                                                       id="title_{{ $business->id }}" 
                                                       class="form-control meta-title-input" 
                                                       placeholder="{{ $business->metaData['auto_title'] }}" 
                                                       value="{{ $business->metaData['manual_title'] }}">
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <span class="badge badge-status {{ $business->metaData['has_custom_title'] ? 'bg-info text-white' : 'bg-light text-secondary border' }}">
                                                    {{ $business->metaData['has_custom_title'] ? 'Custom Override' : 'Auto (' . ucfirst($business->metaData['title_source']) . ')' }}
                                                </span>
                                                <span class="char-count {{ $business->metaData['title_length'] > $rule->title_char_limit ? 'exceeded' : '' }}">
                                                    <span class="current-chars">{{ $business->metaData['title_length'] }}</span>/{{ $rule->title_char_limit }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Description Tag Input --}}
                                    <td class="align-top py-3">
                                        <div class="meta-field-wrap" data-limit="{{ $rule->description_char_limit }}">
                                            <div class="input-group input-group-sm mb-1">
                                                <textarea name="meta_description[{{ $business->id }}]" 
                                                          id="desc_{{ $business->id }}" 
                                                          rows="2" style="min-height: 50px; height: 50px; resize: vertical;" 
                                                          class="form-control meta-desc-input" 
                                                          placeholder="{{ $business->metaData['auto_description'] }}">{{ $business->metaData['manual_description'] }}</textarea>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <span class="badge badge-status {{ $business->metaData['has_custom_description'] ? 'bg-info text-white' : 'bg-light text-secondary border' }}">
                                                    {{ $business->metaData['has_custom_description'] ? 'Custom Override' : 'Auto (' . ucfirst($business->metaData['description_source']) . ')' }}
                                                </span>
                                                <span class="char-count {{ $business->metaData['description_length'] > $rule->description_char_limit ? 'exceeded' : '' }}">
                                                    <span class="current-chars">{{ $business->metaData['description_length'] }}</span>/{{ $rule->description_char_limit }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Actions --}}
                                    <td class="text-end align-top py-3 pe-3">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" 
                                                    class="btn btn-primary btn-save-single" 
                                                    data-biz-id="{{ $business->id }}" 
                                                    title="Save this business">
                                                <em class="icon ni ni-check"></em>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-warning btn-reset-single {{ ($business->metaData['has_custom_title'] || $business->metaData['has_custom_description']) ? '' : 'd-none' }}" 
                                                    data-biz-id="{{ $business->id }}" 
                                                    title="Reset to dynamic auto rule">
                                                <em class="icon ni ni-undo"></em>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No businesses found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center bg-light">
                <div class="text-muted small">
                    Showing {{ $businesses->count() }} businesses. Blank inputs automatically inherit dynamic cascade rules.
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <em class="icon ni ni-save me-1"></em> Save All Overrides
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Modal for Default Rules --}}
<div class="modal fade" id="modalDefaultRules" tabindex="-1" aria-labelledby="modalDefaultRulesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.meta-tags.rules.update') }}" method="POST" id="metaRulesForm">
                @csrf
                <input type="hidden" name="entity_type" value="business_discussions">
                <input type="hidden" name="lang_id" value="{{ $lang_id }}">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="modalDefaultRulesLabel">
                        <em class="icon ni ni-setting me-1"></em> Business Meta Tag Defaults & Cascade Rules
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-primary mb-4">
                        <div class="d-flex">
                            <em class="icon ni ni-info-fill fs-20px me-2"></em>
                            <div>
                                <strong>How the Cascade Rules Work:</strong>
                                <p class="small mb-1 mt-1">
                                    The system evaluates the <strong>Primary</strong> template. If its evaluated length with real variables fits within the configured character limit, it will be used.
                                    If it exceeds the limit, it falls back to <strong>Secondary</strong>, then <strong>Tertiary</strong>, and finally <strong>Quaternary (4°)</strong> for the worst case (used even if it exceeds the limit).
                                </p>
                                
                                <div class="row g-1 mt-2">
                                    @foreach (['[business]', '[category_cap]', '[category_tc]', '[category_lc]', '[entity1_cap]', '[entity1_lc]', '[entities1_cap]', '[entities1_lc]', '[entity1_short_cap]', '[entity1_short_lc]', '[entities1_short_cap]', '[entities1_short_lc]', '[entity2_cap]', '[entity2_lc]', '[entities2_cap]', '[entities2_lc]', '[entity2_short_cap]', '[entity2_short_lc]', '[entities2_short_cap]', '[entities2_short_lc]', '[country1]', '[country2]', '[year]', '[website]'] as $token)
                                        <div class="col-6 col-sm-4 col-md-3"><span class="badge bg-white text-primary border cursor-pointer placeholder-chip font-monospace fs-10px p-1 w-100 text-start" data-token="{{ $token }}">{{ $token }}</span></div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                                        {{-- Title Tag Cascade Section --}}
                    <div class="card border mb-4">
                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                            <strong class="text-dark"><em class="icon ni ni-heading me-1 text-primary"></em> Title Tag Cascade Rules</strong>
                            <div class="d-flex align-items-center gap-2">
                                <label for="rule_title_char_limit" class="small mb-0 text-muted">Character Limit:</label>
                                <input type="number" 
                                       name="title_char_limit" 
                                       id="rule_title_char_limit" 
                                       class="form-control form-control-sm text-center font-weight-bold" 
                                       style="width: 75px;" 
                                       value="{{ $rule->title_char_limit }}" 
                                       min="20" max="255" required>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            {{-- Primary Title --}}
                            <div class="mb-3">
                                <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                                    <span>1&deg; Primary Default <span class="badge bg-light text-primary border">Preferred</span></span>
                                    <span class="text-muted fw-normal fs-11px">Evaluated first; used if length &le; limit</span>
                                </label>
                                <input type="text" name="title_primary" id="rule_title_primary" class="form-control form-control-sm font-monospace" value="{{ $rule->title_primary }}" required>
                            </div>
                            {{-- Secondary Title --}}
                            <div class="mb-3">
                                <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                                    <span>2&deg; Secondary Fallback <span class="badge bg-light text-secondary border">Shorter</span></span>
                                    <span class="text-muted fw-normal fs-11px">Used if Primary exceeds limit</span>
                                </label>
                                <input type="text" name="title_secondary" id="rule_title_secondary" class="form-control form-control-sm font-monospace" value="{{ $rule->title_secondary }}">
                            </div>
                            {{-- Tertiary Title --}}
                            <div class="mb-3">
                                <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                                    <span>3&deg; Tertiary Fallback <span class="badge bg-light text-secondary border">Concise</span></span>
                                    <span class="text-muted fw-normal fs-11px">Used if Secondary also exceeds limit</span>
                                </label>
                                <input type="text" name="title_tertiary" id="rule_title_tertiary" class="form-control form-control-sm font-monospace" value="{{ $rule->title_tertiary }}">
                            </div>
                            {{-- Quaternary Title (4°) --}}
                            <div class="mb-1">
                                <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                                    <span>4&deg; Quaternary Fallback <span class="badge bg-warning text-dark border">Worst Case</span></span>
                                    <span class="text-muted fw-normal fs-11px">Used even if character limit is exceeded</span>
                                </label>
                                <input type="text" name="title_quaternary" id="rule_title_quaternary" class="form-control form-control-sm font-monospace" value="{{ $rule->title_quaternary }}">
                            </div>
                        </div>
                    </div>

                    {{-- Description Tag Cascade Section --}}
                    <div class="card border mb-4">
                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                            <strong class="text-dark"><em class="icon ni ni-file-text me-1 text-primary"></em> Description Tag Cascade Rules</strong>
                            <div class="d-flex align-items-center gap-2">
                                <label for="rule_desc_char_limit" class="small mb-0 text-muted">Character Limit:</label>
                                <input type="number" 
                                       name="description_char_limit" 
                                       id="rule_desc_char_limit" 
                                       class="form-control form-control-sm text-center font-weight-bold" 
                                       style="width: 75px;" 
                                       value="{{ $rule->description_char_limit }}" 
                                       min="30" max="500" required>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            {{-- Primary Description --}}
                            <div class="mb-3">
                                <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                                    <span>1&deg; Primary Default</span>
                                    <span class="text-muted fw-normal fs-11px">Evaluated first; used if length &le; limit</span>
                                </label>
                                <textarea name="description_primary" id="rule_desc_primary" rows="2" style="min-height: 50px; height: 50px; resize: vertical;" class="form-control form-control-sm font-monospace">{{ $rule->description_primary }}</textarea>
                            </div>
                            {{-- Secondary Description --}}
                            <div class="mb-3">
                                <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                                    <span>2&deg; Secondary Fallback</span>
                                    <span class="text-muted fw-normal fs-11px">Used if Primary exceeds limit</span>
                                </label>
                                <textarea name="description_secondary" id="rule_desc_secondary" rows="2" style="min-height: 50px; height: 50px; resize: vertical;" class="form-control form-control-sm font-monospace">{{ $rule->description_secondary }}</textarea>
                            </div>
                            {{-- Tertiary Description --}}
                            <div class="mb-3">
                                <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                                    <span>3&deg; Tertiary Fallback</span>
                                    <span class="text-muted fw-normal fs-11px">Used if Secondary also exceeds limit</span>
                                </label>
                                <textarea name="description_tertiary" id="rule_desc_tertiary" rows="2" style="min-height: 50px; height: 50px; resize: vertical;" class="form-control form-control-sm font-monospace">{{ $rule->description_tertiary }}</textarea>
                            </div>
                            {{-- Quaternary Description (4°) --}}
                            <div class="mb-1">
                                <label class="form-label small fw-bold mb-1 d-flex justify-content-between">
                                    <span>4&deg; Quaternary Fallback <span class="badge bg-warning text-dark border">Worst Case</span></span>
                                    <span class="text-muted fw-normal fs-11px">Used even if character limit is exceeded</span>
                                </label>
                                <textarea name="description_quaternary" id="rule_desc_quaternary" rows="2" style="min-height: 50px; height: 50px; resize: vertical;" class="form-control form-control-sm font-monospace">{{ $rule->description_quaternary }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btnSaveRules">
                        <em class="icon ni ni-check me-1"></em> Save Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.placeholder-chip {
    transition: all 0.2s ease;
    user-select: none;
}
.placeholder-chip:hover {
    background-color: #0971fe !important;
    color: #fff !important;
}
.cursor-pointer {
    cursor: pointer;
}
.char-count {
    font-size: 11px;
    font-weight: 600;
    color: #6c757d;
}
.char-count.exceeded {
    color: #e85347 !important;
    font-weight: 700;
}
.badge-status {
    font-size: 10px;
    padding: 3px 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.meta-title-input:focus, .meta-desc-input:focus {
    border-color: #0971fe;
    box-shadow: 0 0 0 0.2rem rgba(9, 113, 254, 0.15);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Insert placeholder chip into active modal input on click
    $(document).on('click', '.placeholder-chip', function() {
        var token = $(this).data('token');
        var modal = $(this).closest('.modal');
        var activeInput = modal.find('input:focus, textarea:focus');
        if (!activeInput.length) {
            activeInput = modal.find('input[name="title_primary"]');
        }
        if (activeInput.length) {
            var el = activeInput[0];
            var start = el.selectionStart || el.value.length;
            var end = el.selectionEnd || el.value.length;
            var val = el.value;
            el.value = val.substring(0, start) + token + val.substring(end);
            el.selectionStart = el.selectionEnd = start + token.length;
            el.focus();
            $(el).trigger('input');
        }
    });

    // 1. Character Counter & Status Badge Dynamic Update
    function updateCharCount(el) {
        var input = $(el);
        var wrap = input.closest('.meta-field-wrap');
        var limit = parseInt(wrap.data('limit'), 10) || 60;
        var countSpan = wrap.find('.current-chars');
        var counterWrap = wrap.find('.char-count');
        var statusBadge = wrap.find('.badge-status');
        var row = input.closest('tr');
        var resetBtn = row.find('.btn-reset-single');

        var val = input.val();
        var placeholder = input.attr('placeholder') || '';
        var currentText = val.length > 0 ? val : placeholder;
        var len = currentText.length;

        countSpan.text(len);

        if (len > limit) {
            counterWrap.addClass('exceeded');
        } else {
            counterWrap.removeClass('exceeded');
        }

        if (val.length > 0) {
            statusBadge.removeClass('bg-light text-secondary border').addClass('bg-info text-white').text('Custom Override');
            resetBtn.removeClass('d-none');
        } else {
            statusBadge.removeClass('bg-info text-white').addClass('bg-light text-secondary border').text('Auto');
            var otherVal = input.hasClass('meta-title-input') ? row.find('.meta-desc-input').val() : row.find('.meta-title-input').val();
            if (!otherVal || otherVal.trim().length === 0) {
                resetBtn.addClass('d-none');
            }
        }
    }

    $(document).on('input', '.meta-title-input, .meta-desc-input', function() {
        updateCharCount(this);
    });

    // 2. Search Filter for Businesses
    $('#businessSearchFilter').on('input', function() {
        var query = $(this).val().toLowerCase().trim();
        $('.business-row').each(function() {
            var row = $(this);
            var bizName = row.data('biz-name') || '';
            var catName = row.data('cat-name') || '';
            if (query === '' || bizName.indexOf(query) !== -1 || catName.indexOf(query) !== -1) {
                row.show();
            } else {
                row.hide();
            }
        });
    });

    // 3. Quick Save Single Business via AJAX
    $(document).on('click', '.btn-save-single', function(e) {
        e.preventDefault();
        var btn = $(this);
        var bizId = btn.data('biz-id');
        var titleVal = $('#title_' + bizId).val();
        var descVal = $('#desc_' + bizId).val();
        var langId = '{{ $lang_id }}';

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span>');

        $.ajax({
            url: '{{ route("admin.meta-tags.business-discussions.update-single") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                business_id: bizId,
                lang_id: langId,
                meta_title: titleVal,
                meta_description: descVal
            },
            success: function(res) {
                btn.removeClass('btn-primary').addClass('btn-success').html('<em class="icon ni ni-check"></em>');
                if (typeof toastr !== 'undefined') {
                    toastr.success('Business meta tags updated successfully!');
                }

                // If inputs are cleared or updated, update placeholders, char counters and reset button visibility
                var row = btn.closest('tr');
                var titleInput = row.find('.meta-title-input');
                var descInput = row.find('.meta-desc-input');
                if (res.resolved) {
                    titleInput.attr('placeholder', res.resolved.auto_title);
                    descInput.attr('placeholder', res.resolved.auto_description);
                }
                updateCharCount(titleInput);
                updateCharCount(descInput);

                setTimeout(function() {
                    btn.removeClass('btn-success').addClass('btn-primary').prop('disabled', false);
                }, 2000);
            },
            error: function(err) {
                btn.removeClass('btn-primary').addClass('btn-danger').html('<em class="icon ni ni-cross"></em>');
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to save business meta tags.');
                }
                setTimeout(function() {
                    btn.removeClass('btn-danger').addClass('btn-primary').html('<em class="icon ni ni-check"></em>').prop('disabled', false);
                }, 2000);
            }
        });
    });

    // 4. Reset Business to Auto Default via AJAX
    $(document).on('click', '.btn-reset-single', function(e) {
        e.preventDefault();
        var btn = $(this);
        var bizId = btn.data('biz-id');
        var langId = '{{ $lang_id }}';

        if (!confirm('Clear manual overrides and reset this business to dynamic rules?')) {
            return;
        }

        btn.prop('disabled', true);

        $.ajax({
            url: '{{ route("admin.meta-tags.business-discussions.reset-single") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                business_id: bizId,
                lang_id: langId
            },
            success: function(res) {
                var row = btn.closest('tr');
                var titleInput = row.find('.meta-title-input');
                var descInput = row.find('.meta-desc-input');

                titleInput.val('').attr('placeholder', res.resolved.auto_title);
                descInput.val('').attr('placeholder', res.resolved.auto_description);

                updateCharCount(titleInput);
                updateCharCount(descInput);

                btn.addClass('d-none').prop('disabled', false);
                if (typeof toastr !== 'undefined') {
                    toastr.info('Business reset to dynamic auto rules.');
                }
            },
            error: function() {
                btn.prop('disabled', false);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to reset business.');
                }
            }
        });
    });
});
</script>
@endsection
