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
                                <li class="nk-block-tools-opt d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadJsonModal">
                                        <em class="icon ni ni-upload-cloud"></em><span>Upload Features JSON</span>
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="confirmDeleteAll()">
                                        <em class="icon ni ni-trash"></em><span>Delete All Features</span>
                                    </button>
                                    <a href="{{ route('features.create') }}"
                                        class="btn btn-primary btn-localio"><span>Add Feature</span></a>
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

                @if ($features->isEmpty() && !empty($selectedCategoryId))
            <div class="alert alert-light alert-icon border mb-3">
                <em class="icon ni ni-info"></em>
                <div class="d-flex align-items-center justify-content-between w-100">
                    <span>No features found for <strong>{{ $activeCatName ?? "selected category" }}</strong>.</span>
                    <a href="{{ route('features') }}" class="btn btn-xs btn-outline-primary ms-3">Show All Categories</a>
                </div>
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
                                                            @if($feature->category_id)
                                                            <li>
                                                                <a href="{{ route('admin.category-feature-content.create', ['category_id' => $feature->category_id, 'feature_id' => $feature->id]) }}">
                                                                    <em class="icon ni ni-file-text-fill"></em>
                                                                    <span>Feature Category Content</span>
                                                                </a>
                                                            </li>
                                                            @else
                                                            <li>
                                                                <a href="{{ route('admin.category-feature-content.create', ['feature_id' => $feature->id]) }}">
                                                                    <em class="icon ni ni-file-text-fill"></em>
                                                                    <span>Feature Category Content</span>
                                                                </a>
                                                            </li>
                                                            @endif
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
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Upload JSON Modal -->
    <div class="modal fade" id="uploadJsonModal" tabindex="-1" aria-labelledby="uploadJsonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('features.json_upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadJsonModalLabel">Upload Features via JSON</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="form-label" for="category_id">Applicable Category <span class="text-danger">*</span></label>
                            <small class="text-muted d-block mb-1">Only main categories are listed below (subcategories excluded).</small>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ (isset($selectedCategoryId) && $selectedCategoryId == $category->id) ? 'selected' : '' }}>
                                        {{ $category->translated_name ?? ($category->translations->name ?? 'Category #'.$category->id) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label" for="json_file">Upload .JSON File <small class="text-muted">(Optional: upload file or paste JSON below)</small></label>
                            <input type="file" class="form-control" name="json_file" id="json_file" accept=".json,application/json,text/plain" onchange="handleJsonFileSelect(this)">
                        </div>
                        <div class="form-group mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0" for="json_data">Features JSON</label>
                                <button type="button" class="btn btn-xs btn-outline-secondary" onclick="copyExampleFormat()">
                                    <em class="icon ni ni-copy"></em> Copy Example Format
                                </button>
                            </div>
                            <textarea class="form-control font-monospace" name="json_data" id="json_data" rows="8" placeholder='[
  {
    "feature_cap": "Access logs",
    "features_tc": "Access Logs",
    "feature_lc": "access logs"
  },
  {
    "feature_cap": "Website backups",
    "features_tc": "Website Backups",
    "feature_lc": "website backups"
  }
]'></textarea>
                            <div class="alert alert-fill alert-light alert-icon mt-2 py-2 px-3">
                                <em class="icon ni ni-info"></em>
                                <div class="small">
                                    <strong>Supported Formats:</strong> JSON supports all versions with or without brackets:
                                    <code>[feature_cap]</code>, <code>[features_tc]</code> (or <code>[feature_tc]</code>), and <code>[feature_lc]</code> (or standard <code>name</code>). Missing versions will be automatically derived.
                                </div>
                            </div>
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

        function handleJsonFileSelect(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    const textarea = document.getElementById('json_data');
                    if (textarea) {
                        textarea.value = e.target.result;
                    }
                };
                reader.readAsText(file);
            }
        }

        function copyExampleFormat() {
            const exampleFormat = `[
  {
    "feature_cap": "Access logs",
    "features_tc": "Access Logs",
    "feature_lc": "access logs"
  },
  {
    "feature_cap": "Website backups",
    "features_tc": "Website Backups",
    "feature_lc": "website backups"
  }
]`;
            navigator.clipboard.writeText(exampleFormat).then(() => {
                NioApp.Toast('Example JSON format copied to clipboard!', 'info', { position: 'top-right' });
            }).catch(err => {
                const textarea = document.getElementById('json_data');
                textarea.value = exampleFormat;
                NioApp.Toast('Example JSON inserted into text area!', 'info', { position: 'top-right' });
            });
        }

        function confirmDeleteAll() {
            const selectedCatId = '{{ $selectedCategoryId ?? "" }}';
            const activeCatName = '{{ isset($activeCatName) ? addslashes($activeCatName) : "" }}';

            if (selectedCatId) {
                Swal.fire({
                    title: "Delete Features",
                    text: "Category filter active (" + activeCatName + "). Delete only features for this category, or delete ALL features across all categories?",
                    icon: "warning",
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonColor: "#d33",
                    denyButtonColor: "#c82333",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Delete Category Features",
                    denyButtonText: "Delete ALL Features (Global)",
                    cancelButtonText: "Cancel"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('features.delete_all') }}?category_id=" + selectedCatId;
                    } else if (result.isDenied) {
                        Swal.fire({
                            title: "Are you ABSOLUTELY sure?",
                            text: "This will permanently delete EVERY feature across ALL categories from the database. This action cannot be reversed!",
                            icon: "error",
                            showCancelButton: true,
                            confirmButtonColor: "#d33",
                            cancelButtonColor: "#6c757d",
                            confirmButtonText: "Yes, delete ALL features globally!"
                        }).then((finalResult) => {
                            if (finalResult.isConfirmed) {
                                window.location.href = "{{ route('features.delete_all') }}";
                            }
                        });
                    }
                });
            } else {
                Swal.fire({
                    title: "Delete ALL Features?",
                    text: "Are you sure you want to permanently delete ALL features from the entire system? All feature data, translations, and relations will be removed. This cannot be undone!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, delete all features!",
                    cancelButtonText: "Cancel"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('features.delete_all') }}";
                    }
                });
            }
        }
    </script>
@endsection
