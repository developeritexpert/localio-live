@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg">
    {{-- Header Banner --}}
    <div class="nk-block-head d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title mb-1 d-flex align-items-center flex-wrap">
                <span><em class="icon ni ni-tags text-primary me-2"></em>Meta Tags &mdash; Categories</span>
                @if(isset($selectedLanguage))
                    <span class="badge bg-primary ms-2 font-weight-normal fs-12px">{{ $selectedLanguage->name }} ({{ $selectedLanguage->lang_code }})</span>
                @endif
            </h4>
            <div class="text-muted small">
                Manage dynamic 3-tier cascade defaults (Primary, Secondary, Tertiary) based on character limits, or define custom manual overrides for any category.
            </div>
        </div>
        <div class="nk-block-head-content d-flex align-items-center gap-2 mt-2 mt-md-0">
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalDefaultRules">
                <em class="icon ni ni-setting me-1"></em> Configure Defaults & Limits
            </button>
            <div class="d-flex align-items-center ms-2">
                <label class="form-label me-2 mb-0 font-weight-bold small text-nowrap">Language:</label>
                <select class="form-select form-select-sm" id="langSelect" style="min-width: 220px;" onchange="window.location.href='{{ route('admin.meta-tags.category-discussions') }}?lang_id=' + this.value">
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
                        <input type="text" id="categorySearchFilter" class="form-control form-control-sm" placeholder="Filter categories by name...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form for Bulk Saving --}}
    <form action="{{ route('admin.meta-tags.category-discussions.update-bulk') }}" method="POST" id="bulkMetaForm">
        @csrf
        <input type="hidden" name="lang_id" value="{{ $lang_id }}">

        <div class="card card-bordered">
            <div class="card-inner p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="categoriesMetaTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 25%;">Category / Subcategory</th>
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
                            @foreach ($parentCategories as $parent)
                                {{-- Parent Category Row --}}
                                <tr class="category-row bg-white" data-cat-id="{{ $parent->id }}" data-cat-name="{{ strtolower($parent->metaData['name']) }}" data-parent-name="">
                                    <td class="align-top py-3">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary text-white me-2">Parent</span>
                                            <div>
                                                <strong class="fs-15px text-dark d-block">{{ $parent->metaData['name'] }}</strong>
                                                <span class="text-muted small">#{{ $parent->id }} &bull; {{ $parent->subCategories->count() }} subcategories</span>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Title Tag Input --}}
                                    <td class="align-top py-3">
                                        <div class="meta-field-wrap" data-limit="{{ $rule->title_char_limit }}">
                                            <div class="input-group input-group-sm mb-1">
                                                <input type="text" 
                                                       name="meta_title[{{ $parent->id }}]" 
                                                       id="title_{{ $parent->id }}" 
                                                       class="form-control meta-title-input" 
                                                       placeholder="{{ $parent->metaData['auto_title'] }}" 
                                                       value="{{ $parent->metaData['manual_title'] }}">
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <span class="badge badge-status {{ $parent->metaData['has_custom_title'] ? 'bg-info text-white' : 'bg-light text-secondary border' }}">
                                                    {{ $parent->metaData['has_custom_title'] ? 'Custom Override' : 'Auto (' . ucfirst($parent->metaData['title_source']) . ')' }}
                                                </span>
                                                <span class="char-count {{ $parent->metaData['title_length'] > $rule->title_char_limit ? 'exceeded' : '' }}">
                                                    <span class="current-chars">{{ $parent->metaData['title_length'] }}</span>/{{ $rule->title_char_limit }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Description Tag Input --}}
                                    <td class="align-top py-3">
                                        <div class="meta-field-wrap" data-limit="{{ $rule->description_char_limit }}">
                                            <div class="input-group input-group-sm mb-1">
                                                <textarea name="meta_description[{{ $parent->id }}]" 
                                                          id="desc_{{ $parent->id }}" 
                                                          rows="2" style="min-height: 50px; height: 50px; resize: vertical;" 
                                                          class="form-control meta-desc-input" 
                                                          placeholder="{{ $parent->metaData['auto_description'] }}">{{ $parent->metaData['manual_description'] }}</textarea>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <span class="badge badge-status {{ $parent->metaData['has_custom_description'] ? 'bg-info text-white' : 'bg-light text-secondary border' }}">
                                                    {{ $parent->metaData['has_custom_description'] ? 'Custom Override' : 'Auto (' . ucfirst($parent->metaData['description_source']) . ')' }}
                                                </span>
                                                <span class="char-count {{ $parent->metaData['description_length'] > $rule->description_char_limit ? 'exceeded' : '' }}">
                                                    <span class="current-chars">{{ $parent->metaData['description_length'] }}</span>/{{ $rule->description_char_limit }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Actions --}}
                                    <td class="text-end align-top py-3 pe-3">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" 
                                                    class="btn btn-primary btn-save-single" 
                                                    data-cat-id="{{ $parent->id }}" 
                                                    title="Save this category">
                                                <em class="icon ni ni-check"></em>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-warning btn-reset-single {{ ($parent->metaData['has_custom_title'] || $parent->metaData['has_custom_description']) ? '' : 'd-none' }}" 
                                                    data-cat-id="{{ $parent->id }}" 
                                                    title="Reset to dynamic auto rule">
                                                <em class="icon ni ni-undo"></em>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Subcategories Rows --}}
                                @foreach ($parent->subCategories as $sub)
                                    <tr class="category-row subcategory-row bg-light" data-cat-id="{{ $sub->id }}" data-cat-name="{{ strtolower($sub->metaData['name']) }}" data-parent-name="{{ strtolower($parent->metaData['name']) }}">
                                        <td class="align-top py-3 ps-4 border-start border-3 border-primary">
                                            <div class="d-flex align-items-center">
                                                <em class="icon ni ni-curve-down-right text-muted me-2 fs-14px"></em>
                                                <div>
                                                    <strong class="fs-14px text-dark d-block">{{ $sub->metaData['name'] }}</strong>
                                                    <span class="text-muted small">#{{ $sub->id }} &bull; Under {{ $parent->metaData['name'] }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        {{-- Subcategory Title --}}
                                        <td class="align-top py-3">
                                            <div class="meta-field-wrap" data-limit="{{ $rule->title_char_limit }}">
                                                <div class="input-group input-group-sm mb-1">
                                                    <input type="text" 
                                                           name="meta_title[{{ $sub->id }}]" 
                                                           id="title_{{ $sub->id }}" 
                                                           class="form-control meta-title-input" 
                                                           placeholder="{{ $sub->metaData['auto_title'] }}" 
                                                           value="{{ $sub->metaData['manual_title'] }}">
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <span class="badge badge-status {{ $sub->metaData['has_custom_title'] ? 'bg-info text-white' : 'bg-light text-secondary border' }}">
                                                        {{ $sub->metaData['has_custom_title'] ? 'Custom Override' : 'Auto (' . ucfirst($sub->metaData['title_source']) . ')' }}
                                                    </span>
                                                    <span class="char-count {{ $sub->metaData['title_length'] > $rule->title_char_limit ? 'exceeded' : '' }}">
                                                        <span class="current-chars">{{ $sub->metaData['title_length'] }}</span>/{{ $rule->title_char_limit }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        {{-- Subcategory Description --}}
                                        <td class="align-top py-3">
                                            <div class="meta-field-wrap" data-limit="{{ $rule->description_char_limit }}">
                                                <div class="input-group input-group-sm mb-1">
                                                    <textarea name="meta_description[{{ $sub->id }}]" 
                                                              id="desc_{{ $sub->id }}" 
                                                              rows="2" style="min-height: 50px; height: 50px; resize: vertical;" 
                                                              class="form-control meta-desc-input" 
                                                              placeholder="{{ $sub->metaData['auto_description'] }}">{{ $sub->metaData['manual_description'] }}</textarea>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <span class="badge badge-status {{ $sub->metaData['has_custom_description'] ? 'bg-info text-white' : 'bg-light text-secondary border' }}">
                                                        {{ $sub->metaData['has_custom_description'] ? 'Custom Override' : 'Auto (' . ucfirst($sub->metaData['description_source']) . ')' }}
                                                    </span>
                                                    <span class="char-count {{ $sub->metaData['description_length'] > $rule->description_char_limit ? 'exceeded' : '' }}">
                                                        <span class="current-chars">{{ $sub->metaData['description_length'] }}</span>/{{ $rule->description_char_limit }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        {{-- Subcategory Actions --}}
                                        <td class="text-end align-top py-3 pe-3">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" 
                                                        class="btn btn-primary btn-save-single" 
                                                        data-cat-id="{{ $sub->id }}" 
                                                        title="Save this subcategory">
                                                    <em class="icon ni ni-check"></em>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-warning btn-reset-single {{ ($sub->metaData['has_custom_title'] || $sub->metaData['has_custom_description']) ? '' : 'd-none' }}" 
                                                        data-cat-id="{{ $sub->id }}" 
                                                        title="Reset to dynamic auto rule">
                                                    <em class="icon ni ni-undo"></em>
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
            <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
                <span class="text-muted small">
                    Tip: Empty fields automatically use dynamic rules according to character limits. Editing a field saves a manual custom override.
                </span>
                <button type="submit" class="btn btn-primary">
                    <em class="icon ni ni-save me-1"></em> Save All Overrides
                </button>
            </div>
        </div>
    </form>
</div>

{{-- MODAL: Configure Defaults & Character Limits --}}
<div class="modal fade" id="modalDefaultRules" tabindex="-1" aria-labelledby="modalDefaultRulesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.meta-tags.rules.update') }}" method="POST" id="metaRulesForm">
                @csrf
                <input type="hidden" name="entity_type" value="category_discussions">
                <input type="hidden" name="lang_id" value="{{ $lang_id }}">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalDefaultRulesLabel">
                        <em class="icon ni ni-setting text-primary me-2"></em>Configure Default Meta Tag Rules & Character Limits
                        @if(isset($selectedLanguage))
                            <span class="badge bg-primary ms-1 font-weight-normal fs-12px">{{ $selectedLanguage->name }}</span>
                        @endif
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Dynamic Tag Placeholders Legend --}}
                    <div class="alert alert-primary bg-primary-dim border-0 mb-4 py-3 px-3">
                        <div class="small font-monospace mb-2 text-dark">
                            Available tags: [category_cap], [category_tc], [category_lc], [entity1_cap], [entity1_lc], [entities1_cap], [entities1_lc], [entity1_short_cap], [entity1_short_lc], [entities1_short_cap], [entities1_short_lc], [entity2_cap], [entity2_lc], [entities2_cap], [entities2_lc], [entity2_short_cap], [entity2_short_lc], [entities2_short_cap], [entities2_short_lc], [country1], [country2], [year], [website]
                        </div>
                        <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
                            @foreach (['[category_cap]', '[category_tc]', '[category_lc]', '[entity1_cap]', '[entity1_lc]', '[entities1_cap]', '[entities1_lc]', '[entity1_short_cap]', '[entity1_short_lc]', '[entities1_short_cap]', '[entities1_short_lc]', '[entity2_cap]', '[entity2_lc]', '[entities2_cap]', '[entities2_lc]', '[entity2_short_cap]', '[entity2_short_lc]', '[entities2_short_cap]', '[entities2_short_lc]', '[country1]', '[country2]', '[year]', '[website]'] as $token)
                                <div class="col-6 col-sm-4 col-md-3"><span class="badge bg-white text-primary border cursor-pointer placeholder-chip font-monospace fs-10px p-1 w-100 text-start" data-token="{{ $token }}">{{ $token }}</span></div>
                            @endforeach
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
                                    <span class="text-muted fw-normal fs-11px">Used if Secondary exceeds limit</span>
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

                    {{-- Live Simulation Tester --}}
                    <div class="card border bg-light-alt">
                        <div class="card-body p-3">
                            <h6 class="fs-13px text-uppercase text-muted fw-bold mb-2">
                                <em class="icon ni ni-activity-round me-1 text-success"></em> Live Cascade Simulator
                            </h6>
                            <div class="row g-2 align-items-center mb-2">
                                <div class="col-sm-5">
                                    <input type="text" id="testCatInput" class="form-control form-control-sm" placeholder="Test with category name..." value="Shared hosting">
                                </div>
                                <div class="col-sm-7 text-muted small">
                                    Simulating dynamic resolution for: <strong>{{ $selectedLanguage->name ?? 'Selected Language' }}</strong>
                                </div>
                            </div>
                            <div class="p-2 bg-white rounded border mb-2">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <strong>Evaluated Title Tag:</strong>
                                    <span id="testTitleBadge" class="badge bg-primary">Primary (1&deg;)</span>
                                </div>
                                <div id="testTitleOutput" class="fw-bold text-dark font-monospace small"></div>
                                <div class="text-end mt-1"><small id="testTitleCount" class="text-muted"></small></div>
                            </div>
                            <div class="p-2 bg-white rounded border">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <strong>Evaluated Description Tag:</strong>
                                    <span id="testDescBadge" class="badge bg-primary">Primary (1&deg;)</span>
                                </div>
                                <div id="testDescOutput" class="text-secondary font-monospace small"></div>
                                <div class="text-end mt-1"><small id="testDescCount" class="text-muted"></small></div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <em class="icon ni ni-check me-1"></em> Save Rules & Limits
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.font-monospace {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
}
.char-count {
    font-size: 11px;
    color: #6c757d;
    font-weight: 500;
}
.char-count.exceeded {
    color: #e85347 !important;
    font-weight: 700;
}
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
.bg-light-alt {
    background-color: #f5f6fa;
}
.subcategory-row {
    background-color: #fbfcfe !important;
}
</style>

<script>
$(document).ready(function() {
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

    // 1. Interactive Character Counter & Over-limit indicator
    function updateCharCount(element) {
        var input = $(element);
        var wrap = input.closest('.meta-field-wrap');
        var limit = parseInt(wrap.data('limit')) || 60;
        var val = input.val();
        
        // If field is empty, calculate length from placeholder
        var count = val.length > 0 ? val.length : (input.attr('placeholder') ? input.attr('placeholder').length : 0);
        var countEl = wrap.find('.current-chars');
        countEl.text(count);

        if (count > limit) {
            wrap.find('.char-count').addClass('exceeded');
        } else {
            wrap.find('.char-count').removeClass('exceeded');
        }

        // Toggle status badge
        var statusBadge = wrap.find('.badge-status');
        var row = input.closest('tr');
        var resetBtn = row.find('.btn-reset-single');

        if (val.trim().length > 0) {
            statusBadge.removeClass('bg-light text-secondary border').addClass('bg-info text-white').text('Custom Override');
            resetBtn.removeClass('d-none');
        } else {
            statusBadge.removeClass('bg-info text-white').addClass('bg-light text-secondary border').text('Auto');
            // Check if both fields in row are empty
            var otherVal = input.hasClass('meta-title-input') ? row.find('.meta-desc-input').val() : row.find('.meta-title-input').val();
            if (!otherVal || otherVal.trim().length === 0) {
                resetBtn.addClass('d-none');
            }
        }
    }

    $(document).on('input', '.meta-title-input, .meta-desc-input', function() {
        updateCharCount(this);
    });

    // 2. Real-time Search Filter for Categories Table
    $('#categorySearchFilter').on('input', function() {
        var query = $(this).val().toLowerCase().trim();
        $('.category-row').each(function() {
            var row = $(this);
            var catName = row.data('cat-name') || '';
            var parentName = row.data('parent-name') || '';
            if (query === '' || catName.indexOf(query) !== -1 || parentName.indexOf(query) !== -1) {
                row.show();
            } else {
                row.hide();
            }
        });
    });

    // 3. Quick Save Single Category via AJAX
    $(document).on('click', '.btn-save-single', function(e) {
        e.preventDefault();
        var btn = $(this);
        var catId = btn.data('cat-id');
        var titleVal = $('#title_' + catId).val();
        var descVal = $('#desc_' + catId).val();
        var langId = '{{ $lang_id }}';

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span>');

        $.ajax({
            url: '{{ route("admin.meta-tags.category-discussions.update-single") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                category_id: catId,
                lang_id: langId,
                meta_title: titleVal,
                meta_description: descVal
            },
            success: function(res) {
                btn.removeClass('btn-primary').addClass('btn-success').html('<em class="icon ni ni-check"></em>');
                if (typeof toastr !== 'undefined') {
                    toastr.success('Category meta tags updated successfully!');
                }
                setTimeout(function() {
                    btn.removeClass('btn-success').addClass('btn-primary').prop('disabled', false);
                }, 2000);
            },
            error: function(err) {
                btn.removeClass('btn-primary').addClass('btn-danger').html('<em class="icon ni ni-cross"></em>');
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to save category meta tags.');
                }
                setTimeout(function() {
                    btn.removeClass('btn-danger').addClass('btn-primary').html('<em class="icon ni ni-check"></em>').prop('disabled', false);
                }, 2000);
            }
        });
    });

    // 4. Reset Category to Auto Default via AJAX
    $(document).on('click', '.btn-reset-single', function(e) {
        e.preventDefault();
        var btn = $(this);
        var catId = btn.data('cat-id');
        var langId = '{{ $lang_id }}';

        if (!confirm('Clear manual overrides and reset this category to dynamic rules?')) {
            return;
        }

        btn.prop('disabled', true);

        $.ajax({
            url: '{{ route("admin.meta-tags.category-discussions.reset-single") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                category_id: catId,
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
                    toastr.info('Category reset to dynamic auto rules.');
                }
            },
            error: function() {
                btn.prop('disabled', false);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to reset category.');
                }
            }
        });
    });

    // 5. Live Simulation Tester in Modal
    function runSimulation() {
        var catName = $('#testCatInput').val().trim() || 'Shared hosting';
        var year = '{{ date("Y") }}';
        var site = 'Localio';

        var catCap = catName.charAt(0).toUpperCase() + catName.slice(1);
        var catTc = catName.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
        var catLc = catName.toLowerCase();

        function rep(tmpl) {
            if (!tmpl) return '';
            return tmpl.replace(/\[category_cap\]/gi, catCap)
                       .replace(/\[category_tc\]/gi, catTc)
                       .replace(/\[category_lc\]/gi, catLc)
                       .replace(/\[entity1_cap\]/gi, catCap + ' provider')
                       .replace(/\[entity1_lc\]/gi, (catCap + ' provider').toLowerCase())
                       .replace(/\[entities1_cap\]/gi, catCap + ' providers')
                       .replace(/\[entities1_lc\]/gi, (catCap + ' providers').toLowerCase())
                       .replace(/\[entity1_short_cap\]/gi, 'Provider')
                       .replace(/\[entity1_short_lc\]/gi, 'provider')
                       .replace(/\[entities1_short_cap\]/gi, 'Providers')
                       .replace(/\[entities1_short_lc\]/gi, 'providers')
                       .replace(/\[entity2_cap\]/gi, catCap + ' provider')
                       .replace(/\[entity2_lc\]/gi, (catCap + ' provider').toLowerCase())
                       .replace(/\[entities2_cap\]/gi, catCap + ' providers')
                       .replace(/\[entities2_lc\]/gi, (catCap + ' providers').toLowerCase())
                       .replace(/\[entity2_short_cap\]/gi, 'Provider')
                       .replace(/\[entity2_short_lc\]/gi, 'provider')
                       .replace(/\[entities2_short_cap\]/gi, 'Providers')
                       .replace(/\[entities2_short_lc\]/gi, 'providers')
                       .replace(/\[country1\]/gi, 'United States')
                       .replace(/\[country2\]/gi, 'US')
                       .replace(/\[year\]|\[Year\]|\{year\}/gi, year)
                       .replace(/\[website\]|\[Site Name\]|\{site_name\}/gi, site)
                       .replace(/\[category name\]|\[category\]|\{category\}/gi, catName);
        }

        // Test Title
        var titleLimit = parseInt($('#rule_title_char_limit').val()) || 60;
        var t1 = rep($('#rule_title_primary').val());
        var t2 = rep($('#rule_title_secondary').val());
        var t3 = rep($('#rule_title_tertiary').val());
        var t4 = rep($('#rule_title_quaternary').val());

        var finalTitle = '', titleTier = 'primary';
        if (t1 && t1.length <= titleLimit) {
            finalTitle = t1;
            titleTier = 'Primary (1°)';
        } else if (t2 && t2.length <= titleLimit) {
            finalTitle = t2;
            titleTier = 'Secondary (2°)';
        } else if (t3 && t3.length <= titleLimit) {
            finalTitle = t3;
            titleTier = 'Tertiary (3°)';
        } else if (t4) {
            finalTitle = t4;
            titleTier = 'Quaternary (4°)';
        } else {
            finalTitle = t3 || catName;
            titleTier = 'Tertiary (3°)';
        }

        $('#testTitleOutput').text(finalTitle);
        $('#testTitleBadge').text(titleTier);
        $('#testTitleCount').text(finalTitle.length + ' / ' + titleLimit + ' chars');

        // Test Description
        var descLimit = parseInt($('#rule_desc_char_limit').val()) || 160;
        var d1 = rep($('#rule_desc_primary').val());
        var d2 = rep($('#rule_desc_secondary').val());
        var d3 = rep($('#rule_desc_tertiary').val());
        var d4 = rep($('#rule_desc_quaternary').val());

        var finalDesc = '', descTier = 'primary';
        if (d1 && d1.length <= descLimit) {
            finalDesc = d1;
            descTier = 'Primary (1°)';
        } else if (d2 && d2.length <= descLimit) {
            finalDesc = d2;
            descTier = 'Secondary (2°)';
        } else if (d3 && d3.length <= descLimit) {
            finalDesc = d3;
            descTier = 'Tertiary (3°)';
        } else if (d4) {
            finalDesc = d4;
            descTier = 'Quaternary (4°)';
        } else {
            finalDesc = d3 || catName;
            descTier = 'Tertiary (3°)';
        }

        $('#testDescOutput').text(finalDesc);
        $('#testDescBadge').text(descTier);
        $('#testDescCount').text(finalDesc.length + ' / ' + descLimit + ' chars');
    }

    $('#testCatInput, #rule_title_char_limit, #rule_title_primary, #rule_title_secondary, #rule_title_tertiary, #rule_title_quaternary, #rule_desc_char_limit, #rule_desc_primary, #rule_desc_secondary, #rule_desc_tertiary, #rule_desc_quaternary').on('input', runSimulation);

    $('#modalDefaultRules').on('shown.bs.modal', function () {
        runSimulation();
    });
});
</script>
@endsection
