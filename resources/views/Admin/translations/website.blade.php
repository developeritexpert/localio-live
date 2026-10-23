@extends('admin_layout.master')

@section('title', 'Website Strings Localization')

@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between align-items-center">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Website Strings Localization</h3>
                    <p class="nk-block-des text-soft">
                        Localization matrix for static website UI elements and buttons vs. BCP47 language codes.
                    </p>
                </div>
                <div class="nk-block-head-content d-flex flex-wrap gap-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <em class="icon ni ni-download-cloud me-1"></em> Export JSON for ChatGPT
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            @foreach($languages as $lang)
                                <a class="dropdown-item" href="{{ route('translations.website.export-json', ['target_locale' => $lang['code']]) }}">
                                    Export for <strong>{{ $lang['name'] }} ({{ $lang['code'] }})</strong>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                        <em class="icon ni ni-upload-cloud me-1"></em> Import Translated JSON
                    </button>
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
                <em class="icon ni ni-alert-circle me-1"></em> {!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Status Legend -->
        <div class="card card-bordered mb-4">
            <div class="card-inner py-2 px-3 bg-light">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-bold fs-13px text-dark">Status Legend:</span>
                        <span class="d-inline-flex align-items-center fs-12px"><em class="icon ni ni-check-circle-fill text-success fs-15px me-1"></em> Fully Translated</span>
                        <span class="d-inline-flex align-items-center fs-12px"><em class="icon ni ni-cross-circle-fill text-danger fs-15px me-1"></em> Missing</span>
                    </div>
                    <div class="text-muted fs-12px">
                        Total Strings: <strong>{{ $keys->total() }}</strong> &bull; Supported Locales: <strong>{{ count($languages) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter bar -->
        <div class="card card-bordered mb-3">
            <div class="card-inner py-3">
                <form method="GET" action="{{ route('translations.website') }}" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fs-12px mb-1">Search String Key, Name or Text</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search key or default..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-12px mb-1">Field Type</label>
                        <select name="field_type" class="form-select form-select-sm">
                            <option value="">All Field Types</option>
                            @foreach($fieldTypes as $ft)
                                <option value="{{ $ft }}" {{ request('field_type') == $ft ? 'selected' : '' }}>{{ ucfirst($ft) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        <a href="{{ route('translations.website') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                            <th style="min-width: 250px;">UI String Key / Default</th>
                            <th style="min-width: 120px;">Type</th>
                            @foreach($languages as $lang)
                                <th class="text-center" style="min-width: 75px;" title="{{ $lang['name'] }}">
                                    <span class="badge bg-outline-secondary text-dark" style="font-size: 11px;">{{ $lang['code'] }}</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($matrix as $row)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark fs-12px">{{ $row['name'] }} <span class="text-muted fw-normal">({{ $row['key'] }})</span></div>
                                    <div class="text-muted fs-11px">{{ \Illuminate\Support\Str::limit($row['default_value'], 70) }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary">{{ $row['field_type'] }}</span>
                                </td>
                                @foreach($languages as $lang)
                                    @php $status = $row['locales'][$lang['code']]['status'] ?? 'missing'; @endphp
                                    <td class="text-center">
                                        @if($status === 'full')
                                            <em class="icon ni ni-check-circle-fill text-success fs-16px" title="Translated: {{ $row['locales'][$lang['code']]['val'] ?? '' }}"></em>
                                        @else
                                            <em class="icon ni ni-cross-circle-fill text-danger fs-16px opacity-50" title="Missing in {{ $lang['code'] }}"></em>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($languages) + 2 }}" class="text-center py-4 text-muted">
                                    No website strings found matching your search.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($keys->hasPages())
                <div class="card-inner py-3 border-top">
                    {{ $keys->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('translations.website.import-json') }}" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Import Website Translated JSON</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted fs-12px mb-3">
                    Upload the completed JSON file exported earlier or paste ChatGPT's JSON response below.
                </p>
                <div class="mb-3">
                    <label class="form-label fs-12px">Upload JSON File</label>
                    <input type="file" name="json_file" class="form-control" accept=".json,.txt">
                </div>
                <div class="mb-3">
                    <label class="form-label fs-12px">Or Paste JSON Content</label>
                    <textarea name="json_text" class="form-control font-monospace fs-11px" rows="6" placeholder='{"target_locale": "de-DE", "strings": [{"id": 1, "translated_val": "..."}]}'></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm">Import Translations</button>
            </div>
        </form>
    </div>
</div>
@endsection