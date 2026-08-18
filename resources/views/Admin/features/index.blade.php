@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg features">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Features</h3>
                </div>
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em
                                class="icon ni ni-more-v"></em></a>
                        <div class="toggle-expand-content" data-content="pageMenu">
                            <ul class="nk-block-tools g-3 align-items-center">
                                {{-- Category Filter Dropdown --}}
                                <li>
                                    <div class="form-control-wrap" style="min-width: 240px;">
                                        <select class="form-select" id="categoryFilter" onchange="filterByCategory(this.value)">
                                            <option value="">All Categories</option>
                                            @foreach ($categories as $cat)
                                                <option value="{{ $cat->id }}" {{ (isset($selectedCategoryId) && $selectedCategoryId == $cat->id) ? 'selected' : '' }}>
                                                    {{ $cat->translated_name ?? ($cat->translations->name ?? 'Category #'.$cat->id) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </li>
                                @if(getCurrentLanguageID() === 1)
                                <li class="nk-block-tools-opt">
                                    <button class="btn btn-outline-primary d-none d-md-inline-flex me-2" data-bs-toggle="modal" data-bs-target="#uploadJsonModal">
                                        <em class="icon ni ni-upload-cloud"></em><span>Upload Features JSON</span>
                                    </button>
                                    <a href="{{ route('features.create') }}"
                                        class="btn btn-primary d-none d-md-inline-flex btn-localio"><span>Add Feature</span></a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Active Category Filter Badge --}}
        @if(!empty($selectedCategoryId))
            @php
                $activeCat = $categories->firstWhere('id', $selectedCategoryId);
                $activeCatName = $activeCat->translated_name ?? ($activeCat->translations->name ?? 'Category #' . $selectedCategoryId);
            @endphp
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="text-muted small">Filtered by category:</span>
                <span class="badge bg-primary fs-7 d-inline-flex align-items-center px-2 py-1">
                    {{ $activeCatName }} ({{ $features->count() }} features)
                </span>
                <a href="{{ route('features') }}" class="btn btn-xs btn-outline-secondary">
                    <em class="icon ni ni-cross me-1"></em> Clear Filter
                </a>
            </div>
        @endif

        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <table class="datatable-init nowrap nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col"><span class="sub-text">Name</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Category</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                            <th class="nk-tb-col tb-tnx-action"><span>Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($features->isNotEmpty())
                            @foreach ($features as $feature)
                                <tr class="nk-tb-item">
                                    {{-- Feature Name --}}
                                    <td class="nk-tb-col">
                                        <div class="user-info">
                                            <span class="tb-lead">
                                                {{ optional($feature->translations->first())->name ?? 'No name' }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Category --}}
                                    <td class="nk-tb-col">
                                        @if ($feature->category && $feature->category->translations)
                                            {{ $feature->category->translations->name }}
                                        @else
                                            <span class="text-muted">?</span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="nk-tb-col">
                                        <span class="{{ strtolower($feature->status) === 'active' ? 'text-success' : 'text-danger' }}">
                                            {{ ucfirst($feature->status) }}
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="nk-tb-col nk-tb-col-tools">
                                        <ul class="nk-tb-actions gx-1">
                                            <li>
                                                <div class="drodown">
                                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                        data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                    <div class="dropdown-menu dropdown-menu-end" style="height: auto">
                                                        <ul class="link-list-opt no-bdr">
                                                            <li>
                                                                <a href="{{ route('features.edit', $feature->id) }}">
                                                                    <em class="icon ni ni-edit-fill"></em>
                                                                    <span>Edit</span>
                                                                </a>
                                                            </li>
                                                            <li class="removeConfermation"
                                                                data-url="{{ route('features.delete', $feature->id) }}">
                                                                <a class="delete" href="{{ route('features.delete', $feature->id) }}">
                                                                    <em class="icon ni ni-trash-fill"></em>
                                                                    <span>Remove</span>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="nk-tb-item">
                                <td class="nk-tb-col text-center py-4" colspan="4">
                                    <span class="text-muted">No features found{{ !empty($selectedCategoryId) ? ' for the selected category' : '' }}.</span>
                                    @if(!empty($selectedCategoryId))
                                        <div class="mt-2">
                                            <a href="{{ route('features') }}" class="btn btn-sm btn-outline-primary">Show All Categories</a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Upload JSON Modal -->
    <div class="modal fade" id="uploadJsonModal" tabindex="-1" aria-labelledby="uploadJsonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('features.json_upload') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadJsonModalLabel">Upload Features via JSON</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="form-label" for="category_id">Applicable Category</label>
                            <small class="text-muted d-block mb-1">Only main categories are listed below (subcategories excluded).</small>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->translated_name ?? ($category->translations->name ?? 'Category #'.$category->id) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0" for="json_data">Features JSON</label>
                                <button type="button" class="btn btn-xs btn-outline-secondary" onclick="copyExampleFormat()">
                                    <em class="icon ni ni-copy"></em> Copy Example Format
                                </button>
                            </div>
                            <textarea class="form-control" name="json_data" id="json_data" rows="8" placeholder='[
  { "name": "SSL included", "description": "Free SSL certificate provided" },
  { "name": "Website backups", "description": "Automated daily backups" }
]' required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" style="background:#F9633B">Upload Features</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function filterByCategory(categoryId) {
            let url = new URL(window.location.href);
            if (categoryId) {
                url.searchParams.set('category_id', categoryId);
            } else {
                url.searchParams.delete('category_id');
            }
            window.location.href = url.toString();
        }

        function copyExampleFormat() {
            const exampleFormat = `[
  { "name": "SSL included", "description": "Free SSL certificate provided" },
  { "name": "Website backups", "description": "Automated daily backups" }
]`;
            navigator.clipboard.writeText(exampleFormat).then(() => {
                NioApp.Toast('Example JSON format copied to clipboard!', 'info', { position: 'top-right' });
            }).catch(err => {
                const textarea = document.getElementById('json_data');
                textarea.value = exampleFormat;
                NioApp.Toast('Example JSON inserted into text area!', 'info', { position: 'top-right' });
            });
        }
    </script>
@endsection
