@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg">
    <div class="nk-block-head d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">Category Feature Content</h4>
            <p class="text-muted">Manage unique descriptions and dynamic text sections (H2 & H3) for Category + Feature landing pages.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('admin.category-feature-content.export', request()->query()) }}" class="btn btn-outline-light bg-white text-dark">
                <em class="icon ni ni-download"></em> Export JSON
            </a>
            <a href="{{ route('admin.category-feature-content.json-import-view') }}" class="btn btn-outline-primary bg-white">
                <em class="icon ni ni-file-code"></em> Bulk JSON Import
            </a>
            <a href="{{ route('admin.category-feature-content.create') }}" class="btn btn-primary" style="background:#F9633B; border-color:#F9633B;">
                <em class="icon ni ni-plus"></em> Add New Content
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-icon mb-3">
            <em class="icon ni ni-check-circle"></em> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-icon mb-3">
            <em class="icon ni ni-cross-circle"></em> {{ session('error') }}
        </div>
    @endif

    <!-- Filter Card -->
    <div class="card card-bordered mb-4 mt-5">
        <div class="card-inner py-3">
            <form method="GET" action="{{ route('admin.category-feature-content.index') }}">
                <div class="row g-3 align-items-center">
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-bold">Language</label>
                        <select name="lang_id" class="form-select form-select-sm">
                            @foreach($languages as $lang)
                                <option value="{{ $lang->id }}" {{ $lang_id == $lang->id ? 'selected' : '' }}>
                                    {{ $lang->lang_code }} - {{ $lang->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-bold">Category</label>
                        <select name="category_id" class="form-select form-select-sm js-select2">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $selectedCategoryId == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->display_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-bold">Feature</label>
                        <select name="feature_id" class="form-select form-select-sm js-select2">
                            <option value="">All Features</option>
                            @foreach($features as $feat)
                                <option value="{{ $feat->id }}" {{ $selectedFeatureId == $feat->id ? 'selected' : '' }}>
                                    {{ $feat->display_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6 d-flex align-items-end gap-2">
                        <div class="flex-grow-1">
                            <label class="form-label small fw-bold">Search</label>
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search keywords..." value="{{ $searchTerm }}">
                        </div>
                        <button type="submit" class="btn btn-sm btn-dark" style="margin-top: 24px;">Filter</button>
                        @if($selectedCategoryId || $selectedFeatureId || $searchTerm)
                            <a href="{{ route('admin.category-feature-content.index') }}" class="btn btn-sm btn-light" style="margin-top: 24px;">Reset</a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Content Table -->
    <div class="card card-bordered">
        <div class="card-inner p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="min-width: 180px;">Category</th>
                            <th style="min-width: 180px;">Feature</th>
                            <th>Lang</th>
                            <th style="min-width: 250px;">Top Description Preview</th>
                            <th class="text-center">Text Sections</th>
                            <th style="min-width: 120px;">Last Updated</th>
                            <th class="text-end" style="min-width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contents as $item)
                            @php
                                $cName = $item->category?->translations->firstWhere('lang_id', $item->lang_id)?->name 
                                    ?? ($item->category?->translations->first()?->name ?? 'Category #'.$item->category_id);
                                $fName = $item->feature?->translations->firstWhere('lang_id', $item->lang_id)?->name 
                                    ?? ($item->feature?->translations->first()?->name ?? 'Feature #'.$item->feature_id);
                                $secArr = !empty($item->text_sections) ? (is_array($item->text_sections) ? $item->text_sections : json_decode($item->text_sections, true)) : [];
                                $secCount = is_array($secArr) ? count($secArr) : 0;
                            @endphp
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $cName }}</span>
                                    <small class="d-block text-muted">ID: {{ $item->category_id }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-primary border px-2 py-1" style="font-size: 12px; font-weight: 600;">
                                        {{ $fName }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary" style="font-size: 11px;">
                                        {{ $item->language?->lang_code ?? 'ID: '.$item->lang_id }}
                                    </span>
                                </td>
                                <td>
                                    @if(!empty($item->description))
                                        <div class="text-muted small" style="max-height: 44px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                            {{ strip_tags($item->description) }}
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic small">Default category description</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($secCount > 0)
                                        <span class="badge bg-success" style="font-size: 11px;">{{ $secCount }} H2 {{ $secCount == 1 ? 'Section' : 'Sections' }}</span>
                                    @else
                                        <span class="badge bg-light text-muted border" style="font-size: 11px;">None</span>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    {{ $item->updated_at->format('M d, Y') }}
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('admin.category-feature-content.edit', $item->id) }}" class="btn btn-sm btn-icon btn-light" title="Edit">
                                            <em class="icon ni ni-edit"></em>
                                        </a>
                                        <form method="POST" action="{{ route('admin.category-feature-content.destroy', $item->id) }}" onsubmit="return confirm('Are you sure you want to delete this content?');" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Delete">
                                                <em class="icon ni ni-trash"></em>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <em class="icon ni ni-file-docs mb-2" style="font-size: 32px;"></em>
                                    <p class="mb-2">No category feature content found.</p>
                                    <a href="{{ route('admin.category-feature-content.create') }}" class="btn btn-sm btn-primary">Add New</a> or 
                                    <a href="{{ route('admin.category-feature-content.json-import-view') }}" class="btn btn-sm btn-outline-primary">Import via JSON</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($contents->hasPages())
                <div class="card-inner border-top">
                    {{ $contents->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
