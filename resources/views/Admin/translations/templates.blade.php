@extends('admin_layout.master')

@section('title', 'Content Templates Localization')

@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between align-items-center">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Content Templates Localization</h3>
                    <p class="nk-block-des text-soft">
                        Localization matrix for automated content templates and boilerplate copy vs. BCP47 language codes.
                    </p>
                </div>
                <div class="nk-block-head-content d-flex flex-wrap gap-2">
                    <a href="{{ route('translations.businesses') }}" class="btn btn-outline-primary btn-sm">
                        <em class="icon ni ni-building me-1"></em> Businesses Matrix
                    </a>
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
                        Total Templates: <strong>{{ $templates->total() }}</strong> &bull; Supported Locales: <strong>{{ count($languages) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter bar -->
        <div class="card card-bordered mb-3">
            <div class="card-inner py-3">
                <form method="GET" action="{{ route('translations.templates') }}" class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fs-12px mb-1">Search Template Field Key or Headline</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search field key or text..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-12px mb-1">Section Group</label>
                        <select name="section_group" class="form-select form-select-sm">
                            <option value="">All Groups</option>
                            @foreach($sectionGroups as $grp)
                                <option value="{{ $grp }}" {{ request('section_group') == $grp ? 'selected' : '' }}>{{ ucfirst($grp) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        <a href="{{ route('translations.templates') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                            <th style="min-width: 250px;">Template Headline / Key</th>
                            <th style="min-width: 120px;">Section Group</th>
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
                                    <div class="fw-bold text-dark fs-12px">{{ $row['name'] }}</div>
                                    <div class="text-muted fs-11px">Key: {{ $row['key'] }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary">{{ $row['section_group'] }}</span>
                                </td>
                                @foreach($languages as $lang)
                                    @php $status = $row['locales'][$lang['code']]['status'] ?? 'missing'; @endphp
                                    <td class="text-center">
                                        @if($status === 'full')
                                            <em class="icon ni ni-check-circle-fill text-success fs-16px" title="Translated ({{ $lang['code'] }})"></em>
                                        @else
                                            <em class="icon ni ni-cross-circle-fill text-danger fs-16px opacity-50" title="Missing ({{ $lang['code'] }})"></em>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($languages) + 2 }}" class="text-center py-4 text-muted">
                                    No content templates found matching your search.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($templates->hasPages())
                <div class="card-inner py-3 border-top">
                    {{ $templates->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection