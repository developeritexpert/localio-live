@extends('admin_layout.master')

@section('title', 'Business Translations Matrix')

@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between align-items-center">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Business Translations Matrix</h3>
                    <p class="nk-block-des text-soft">
                        Overview of business localization coverage across all active BCP47 language codes.
                    </p>
                </div>
                <div class="nk-block-head-content d-flex flex-wrap gap-2">
                    <a href="{{ route('translations.reviews') }}" class="btn btn-outline-primary btn-sm">
                        <em class="icon ni ni-comments me-1"></em> Review Translations
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

        <!-- Translation Legend & Stats -->
        <div class="card card-bordered mb-4">
            <div class="card-inner py-2 px-3 bg-light">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-bold fs-13px text-dark">Status Legend:</span>
                        <span class="d-inline-flex align-items-center fs-12px"><em class="icon ni ni-check-circle-fill text-success fs-15px me-1"></em> Fully Translated</span>
                        <span class="d-inline-flex align-items-center fs-12px"><em class="icon ni ni-pie-fill text-warning fs-15px me-1"></em> Partially Translated</span>
                        <span class="d-inline-flex align-items-center fs-12px"><em class="icon ni ni-cross-circle-fill text-danger fs-15px me-1"></em> Missing</span>
                    </div>
                    <div class="text-muted fs-12px">
                        Showing <strong>{{ $businesses->total() }}</strong> businesses &bull; <strong>{{ count($languages) }}</strong> BCP47 locales
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter bar -->
        <div class="card card-bordered mb-3">
            <div class="card-inner py-3">
                <form method="GET" action="{{ route('translations.businesses') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fs-12px mb-1">Search Business</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Title, slug, ID..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-12px mb-1">Category</label>
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->translation->name ?? ($cat->category_cap ?? ($cat->name ?? 'Category #' . $cat->id)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-12px mb-1">Country</label>
                        <select name="country_id" class="form-select form-select-sm">
                            <option value="">All Countries</option>
                            @foreach($countries as $cnt)
                                <option value="{{ $cnt->id }}" {{ request('country_id') == $cnt->id ? 'selected' : '' }}>{{ $cnt->country_name ?? $cnt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        <a href="{{ route('translations.businesses') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Matrix Table -->
        <div class="card card-bordered card-preview">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="fs-12px text-muted">
                            <th style="min-width: 240px;">Business / ID</th>
                            <th style="min-width: 140px;">Category</th>
                            <th style="min-width: 100px;">Country</th>
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
                                    <div class="fw-bold text-dark fs-13px">{{ $row['business']->title ?? 'Business #' . $row['business']->id }}</div>
                                    <span class="text-muted fs-11px">ID: {{ $row['business']->id }} &bull; Slug: {{ $row['business']->slug ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $row['business']->category->name ?? '—' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary">{{ $row['business']->countries->first()->country_name ?? $row['business']->countries->first()->name ?? '—' }}</span>
                                </td>
                                @foreach($languages as $lang)
                                    @php $status = $row['locales'][$lang['code']]['status'] ?? 'missing'; @endphp
                                    <td class="text-center">
                                        @if($status === 'full')
                                            <em class="icon ni ni-check-circle-fill text-success fs-16px" title="Fully Translated ({{ $lang['code'] }})"></em>
                                        @elseif($status === 'partial')
                                            <em class="icon ni ni-pie-fill text-warning fs-16px" title="Partially Translated ({{ $lang['code'] }})"></em>
                                        @else
                                            <em class="icon ni ni-cross-circle-fill text-danger fs-16px opacity-50" title="Missing Translation ({{ $lang['code'] }})"></em>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($languages) + 3 }}" class="text-center py-4 text-muted">
                                    No businesses found matching your criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($businesses->hasPages())
                <div class="card-inner py-3 border-top">
                    {{ $businesses->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection