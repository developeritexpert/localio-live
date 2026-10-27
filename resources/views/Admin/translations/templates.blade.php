@extends('admin_layout.master')

@section('title', 'Page & Content Localization')

@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between align-items-center flex-wrap">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Page Content Localization</h3>
                    <p class="nk-block-des text-soft">
                        Localization matrix for actual rendered frontend text across Category, Subcategory, Feature, and Business pages.
                        All variables are pre-interpolated into English text so translations maintain 100% natural grammar without placeholder errors.
                    </p>
                </div>
                <div class="nk-block-head-content d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exportJsonModal">
                        <em class="icon ni ni-download me-1"></em> Export JSON
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#importJsonModal">
                        <em class="icon ni ni-upload me-1"></em> Import JSON
                    </button>
                    <a href="{{ route('translations.batch-jobs') }}" class="btn btn-primary btn-sm">
                        <em class="icon ni ni-layers me-1"></em> Batch Jobs
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <em class="icon ni ni-check-circle me-1"></em> {!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <em class="icon ni ni-cross-circle me-1"></em> {!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Entity / Page Type Tabs --}}
        <ul class="nav nav-tabs nav-tabs-mb-icon nav-tabs-card mb-3">
            <li class="nav-item">
                <a class="nav-link {{ $activeTab === 'categories' ? 'active' : '' }}" href="{{ route('translations.templates', ['tab' => 'categories']) }}">
                    <em class="icon ni ni-folder me-1"></em> Categories & Subcategories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab === 'businesses' ? 'active' : '' }}" href="{{ route('translations.templates', ['tab' => 'businesses']) }}">
                    <em class="icon ni ni-building me-1"></em> Businesses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab === 'features' ? 'active' : '' }}" href="{{ route('translations.templates', ['tab' => 'features']) }}">
                    <em class="icon ni ni-grid-alt me-1"></em> Features
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab === 'global_templates' ? 'active' : '' }}" href="{{ route('translations.templates', ['tab' => 'global_templates']) }}">
                    <em class="icon ni ni-template me-1"></em> Global Boilerplate (No Variables)
                </a>
            </li>
        </ul>

        <!-- Status Legend -->
        <div class="card card-bordered mb-3">
            <div class="card-inner py-2 px-3 bg-light">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        <span class="fw-bold fs-13px text-dark">Status Legend:</span>
                        <span class="d-inline-flex align-items-center fs-12px">
                            <em class="icon ni ni-check-circle-fill text-success fs-15px me-1"></em> Fully Translated
                        </span>
                        <span class="d-inline-flex align-items-center fs-12px">
                            <em class="icon ni ni-alert-circle-fill text-warning fs-15px me-1"></em> Outdated (US-EN Changed)
                        </span>
                        <span class="d-inline-flex align-items-center fs-12px">
                            <em class="icon ni ni-cross-circle-fill text-danger fs-15px me-1"></em> Missing
                        </span>
                    </div>
                    <div class="text-muted fs-12px">
                        Total Sections: <strong>{{ $items->total() }}</strong> &bull; Supported Locales: <strong>{{ count($languages) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card card-bordered mb-3">
            <div class="card-inner py-3">
                <form method="GET" action="{{ route('translations.templates') }}" class="row g-2 align-items-end">
                    <input type="hidden" name="tab" value="{{ $activeTab }}">

                    <div class="col-md-4">
                        <label class="form-label fs-12px mb-1">Search Page, Section or Real Text</label>
                        <div class="form-control-wrap">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search headline, text, or key..." value="{{ $search ?? '' }}">
                        </div>
                    </div>

                    @if($activeTab === 'categories')
                    <div class="col-md-3">
                        <label class="form-label fs-12px mb-1">Category Filter</label>
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            @foreach($allCategories as $c)
                                <option value="{{ $c->id }}" {{ (isset($selectedCategoryId) && $selectedCategoryId == $c->id) ? 'selected' : '' }}>
                                    {{ $c->translations->first()?->name ?? $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="col-md-3">
                        <label class="form-label fs-12px mb-1">Section Group</label>
                        <select name="section_group" class="form-select form-select-sm">
                            <option value="">All Groups</option>
                            @foreach($sectionGroups as $grp)
                                <option value="{{ $grp }}" {{ ($sectionGroup ?? '') === $grp ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $grp)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        <a href="{{ route('translations.templates', ['tab' => $activeTab]) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Matrix Table -->
        <div class="card card-bordered card-preview">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light fs-12px text-muted">
                        <tr>
                            <th style="min-width: 380px;">Page / Entity & Real English Frontend Text</th>
                            <th style="min-width: 140px;">Section Key</th>
                            @foreach($languages as $lang)
                                <th class="text-center" style="min-width: 70px;" title="{{ $lang['name'] }}">
                                    <span class="badge bg-outline-secondary text-dark" style="font-size: 11px;">{{ $lang['code'] }}</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($matrix as $row)
                            <tr>
                                {{-- Left column: Entity / Page context + Full English resolved text --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <span class="badge bg-primary fs-11px">{{ $row['entity_title'] }}</span>
                                        <span class="badge bg-light text-secondary fs-11px">{{ $row['page_type'] }}</span>
                                        @if($row['page_url'] !== '#')
                                            <a href="{{ $row['page_url'] }}" target="_blank" class="small text-primary text-decoration-none">
                                                <em class="icon ni ni-external"></em> View
                                            </a>
                                        @endif
                                    </div>
                                    <div class="fw-bold text-dark fs-13px mb-1">
                                        {{ $row['source_headline'] ? strip_tags($row['source_headline']) : '—' }}
                                    </div>
                                    @if(!empty($row['source_subtext']))
                                        <div class="text-muted fs-11px" style="max-height: 48px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                            {!! strip_tags($row['source_subtext']) !!}
                                        </div>
                                    @endif
                                </td>

                                {{-- Section Key & Group --}}
                                <td>
                                    <span class="badge bg-outline-light text-dark fs-11px">{{ $row['field_key'] }}</span>
                                    <div class="text-muted fs-11px mt-1">{{ $row['section_group'] }}</div>
                                </td>

                                {{-- Language Matrix Badges with Click-to-Translate --}}
                                @foreach($languages as $lang)
                                    @php 
                                        $loc = $row['locales'][$lang['code']] ?? ['status' => 'missing', 'lang_id' => null];
                                        $status = $loc['status'];
                                        $isEn = (strtolower($lang['code']) === 'en-us' || $loc['lang_id'] == 1);
                                    @endphp
                                    <td class="text-center">
                                        @if($isEn)
                                            <span class="badge bg-success-dim text-success" title="English Source Text">
                                                <em class="icon ni ni-check"></em>
                                            </span>
                                        @elseif($status === 'full')
                                            <button type="button" class="btn btn-trigger btn-icon text-success" 
                                                    title="Translated ({{ $lang['code'] }}). Click to view/edit."
                                                    onclick="openTranslateModal('{{ $row['entity_type'] }}', {{ $row['entity_id'] }}, '{{ $row['field_key'] }}', {{ $loc['lang_id'] }}, '{{ $lang['code'] }}', '{{ addslashes($row['entity_title']) }}')">
                                                <em class="icon ni ni-check-circle-fill fs-16px"></em>
                                            </button>
                                        @elseif($status === 'outdated')
                                            <button type="button" class="btn btn-trigger btn-icon text-warning" 
                                                    title="US-EN Text Changed! Re-translation required ({{ $lang['code'] }}). Click to update."
                                                    onclick="openTranslateModal('{{ $row['entity_type'] }}', {{ $row['entity_id'] }}, '{{ $row['field_key'] }}', {{ $loc['lang_id'] }}, '{{ $lang['code'] }}', '{{ addslashes($row['entity_title']) }}')">
                                                <em class="icon ni ni-alert-circle-fill fs-16px"></em>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-trigger btn-icon text-danger opacity-50" 
                                                    title="Missing ({{ $lang['code'] }}). Click to translate."
                                                    onclick="openTranslateModal('{{ $row['entity_type'] }}', {{ $row['entity_id'] }}, '{{ $row['field_key'] }}', {{ $loc['lang_id'] }}, '{{ $lang['code'] }}', '{{ addslashes($row['entity_title']) }}')">
                                                <em class="icon ni ni-cross-circle-fill fs-16px"></em>
                                            </button>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($languages) + 2 }}" class="text-center py-4 text-muted">
                                    No page content sections found matching your search.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($items->hasPages() || $items->total() > 0)
                <div class="card-inner py-3 border-top d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted small">
                        Showing <strong>{{ $items->firstItem() ?? 0 }}</strong> to <strong>{{ $items->lastItem() ?? 0 }}</strong> of <strong>{{ $items->total() }}</strong> page sections
                    </div>
                    <div>
                        {{ $items->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Interactive Translation Modal --}}
<div class="modal fade" id="translateSectionModal" tabindex="-1" aria-labelledby="translateSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="translateSectionModalLabel">Translate Page Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-fill alert-light alert-icon py-2 px-3 mb-3">
                    <em class="icon ni ni-info"></em>
                    <div class="small">
                        <strong id="modalEntityTitle">Entity</strong> &bull; Section: <code id="modalFieldKey">field_key</code> &bull; Target Locale: <strong id="modalLocaleBadge" class="text-primary">de-DE</strong>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">English Source Headline (Rendered on Page without variables):</label>
                    <div class="p-2 border rounded bg-light text-dark fs-13px" id="modalSourceHeadline">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">English Source Subtext (Rendered on Page without variables):</label>
                    <div class="p-2 border rounded bg-light text-muted fs-12px" id="modalSourceSubtext" style="max-height: 120px; overflow-y: auto;">—</div>
                </div>

                <hr class="my-3">

                <input type="hidden" id="modalEntityType">
                <input type="hidden" id="modalEntityId">
                <input type="hidden" id="modalFieldKeyVal">
                <input type="hidden" id="modalLangId">

                <div class="mb-3">
                    <label class="form-label fw-bold" for="modalTranslatedHeadline">
                        Translated Headline (<span id="modalTargetLangLabel">Target</span>) <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control" id="modalTranslatedHeadline" rows="2" placeholder="Enter translated headline..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold" for="modalTranslatedSubtext">
                        Translated Subtext (<span id="modalTargetLangLabel2">Target</span>)
                    </label>
                    <textarea class="form-control" id="modalTranslatedSubtext" rows="4" placeholder="Enter translated subtext..."></textarea>
                </div>

                <div id="modalStatusAlert" class="d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnSaveTranslation" onclick="saveSectionTranslation()">Save Translation</button>
            </div>
        </div>
    </div>
</div>

{{-- Export JSON Modal --}}
<div class="modal fade" id="exportJsonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('translations.templates.export-json') }}" method="GET">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                <div class="modal-header">
                    <h5 class="modal-title">Export Missing / Outdated Strings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">
                        Export all missing and outdated page section texts without variables for the selected target language.
                        You can translate this JSON directly with ChatGPT or your translation pipeline.
                    </p>
                    <div class="form-group mb-3">
                        <label class="form-label">Target Locale</label>
                        <select name="target_locale" class="form-select">
                            @foreach($languages as $lang)
                                @if(strtolower($lang['code']) !== 'en-us')
                                    <option value="{{ $lang['code'] }}" {{ $lang['code'] === 'de-DE' ? 'selected' : '' }}>
                                        {{ $lang['code'] }} - {{ $lang['name'] }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Download JSON</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Import JSON Modal --}}
<div class="modal fade" id="importJsonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('translations.templates.import-json') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Import Translated JSON</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">
                        Upload the translated JSON file or paste JSON text below to update page section translations across the site.
                    </p>
                    <div class="form-group mb-3">
                        <label class="form-label">Upload JSON File</label>
                        <input type="file" name="json_file" class="form-control" accept=".json,application/json,text/plain">
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Or Paste JSON</label>
                        <textarea name="json_text" rows="8" class="form-control font-monospace" placeholder='{ "target_locale": "de-DE", "strings": [ ... ] }'></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import Translations</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openTranslateModal(entityType, entityId, fieldKey, langId, localeCode, entityTitle) {
        document.getElementById('modalEntityType').value = entityType;
        document.getElementById('modalEntityId').value = entityId;
        document.getElementById('modalFieldKeyVal').value = fieldKey;
        document.getElementById('modalLangId').value = langId;

        document.getElementById('modalEntityTitle').innerText = entityTitle;
        document.getElementById('modalFieldKey').innerText = fieldKey;
        document.getElementById('modalLocaleBadge').innerText = localeCode;
        document.getElementById('modalTargetLangLabel').innerText = localeCode;
        document.getElementById('modalTargetLangLabel2').innerText = localeCode;

        document.getElementById('modalSourceHeadline').innerText = 'Loading...';
        document.getElementById('modalSourceSubtext').innerText = 'Loading...';
        document.getElementById('modalTranslatedHeadline').value = '';
        document.getElementById('modalTranslatedSubtext').value = '';

        const modal = new bootstrap.Modal(document.getElementById('translateSectionModal'));
        modal.show();

        // Fetch translation details via AJAX
        fetch(`{{ route('translations.get-section-translation') }}?entity_type=${entityType}&entity_id=${entityId}&field_key=${fieldKey}&lang_id=${langId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('modalSourceHeadline').innerText = data.source_headline || '—';
                    document.getElementById('modalSourceSubtext').innerHTML = data.source_subtext || '—';
                    document.getElementById('modalTranslatedHeadline').value = data.translated_headline || '';
                    document.getElementById('modalTranslatedSubtext').value = data.translated_subtext || '';
                }
            })
            .catch(err => {
                console.error('Failed to load section translation:', err);
            });
    }

    function saveSectionTranslation() {
        const btn = document.getElementById('btnSaveTranslation');
        btn.disabled = true;
        btn.innerText = 'Saving...';

        const payload = {
            _token: '{{ csrf_token() }}',
            entity_type: document.getElementById('modalEntityType').value,
            entity_id: document.getElementById('modalEntityId').value,
            field_key: document.getElementById('modalFieldKeyVal').value,
            lang_id: document.getElementById('modalLangId').value,
            translated_headline: document.getElementById('modalTranslatedHeadline').value,
            translated_subtext: document.getElementById('modalTranslatedSubtext').value,
            source_headline: document.getElementById('modalSourceHeadline').innerText,
            source_subtext: document.getElementById('modalSourceSubtext').innerHTML,
        };

        fetch('{{ route('translations.save-section-translation') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerText = 'Save Translation';
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('translateSectionModal')).hide();
                window.location.reload();
            } else {
                alert('Failed to save translation: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerText = 'Save Translation';
            alert('An error occurred while saving.');
            console.error(err);
        });
    }
</script>
@endsection