<div>
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Pre-defined Category Pros & Cons</h3>
                <div class="nk-block-des text-soft">
                    <p>Manage pre-defined Pros & Cons assigned per main Category for user review selection.</p>
                </div>
            </div>
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <button class="btn btn-outline-primary me-2" wire:click="openJsonModal">
                        <em class="icon ni ni-code"></em> <span>Paste JSON</span>
                    </button>
                    <button class="btn btn-primary" wire:click="openAddModal">
                        <em class="icon ni ni-plus"></em> <span>Add Pro/Con</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filters & Search -->
    <div class="card card-bordered card-preview mb-4">
        <div class="card-inner">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select class="form-select" wire:model.live="selectedCategoryFilter">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->categoryTranslations->first()->name ?? $cat->categoryTranslations->first()->title ?? 'Category #'.$cat->id }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select class="form-select" wire:model.live="selectedTypeFilter">
                        <option value="">All Types</option>
                        <option value="pro">Pro (+)</option>
                        <option value="con">Con (-)</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" placeholder="Search Pro/Con text..." wire:model.live.debounce.300ms="search">
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card card-bordered card-preview">
        <div class="card-inner p-0">
            <table class="nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                <thead>
                    <tr class="nk-tb-item nk-tb-head">
                        <th class="nk-tb-col"><span class="sub-text">Category</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Type</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Text</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                        <th class="nk-tb-col tb-tnx-action text-end"><span class="sub-text">Action</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr class="nk-tb-item">
                            <td class="nk-tb-col">
                                <div class="user-card">
                                    <div class="user-info">
                                        <span class="tb-lead">{{ $item->category->categoryTranslations->first()->name ?? $item->category->categoryTranslations->first()->title ?? 'Category #'.$item->category_id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="nk-tb-col">
                                @if($item->type === 'pro')
                                    <span class="badge bg-success-dim text-success"><em class="icon ni ni-plus"></em> Pro</span>
                                @else
                                    <span class="badge bg-danger-dim text-danger"><em class="icon ni ni-minus"></em> Con</span>
                                @endif
                            </td>
                            <td class="nk-tb-col">
                                <span class="tb-amount">{{ $item->text }}</span>
                            </td>
                            <td class="nk-tb-col">
                                <button type="button" class="btn btn-xs {{ $item->status ? 'btn-outline-success' : 'btn-outline-secondary' }}" wire:click="toggleStatus({{ $item->id }})">
                                    {{ $item->status ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="nk-tb-col nk-tb-col-tools text-end">
                                <ul class="nk-tb-actions gx-1 justify-end">
                                    <li>
                                        <div class="drodown">
                                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                            <div class="dropdown-menu dropdown-menu-end" style="height: auto">
                                                <ul class="link-list-opt no-bdr">
                                                    <li>
                                                        <a href="javascript:void(0)" wire:click="openEditModal({{ $item->id }})">
                                                            <em class="icon ni ni-edit-fill"></em><span>Edit</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)" class="text-danger" wire:click="delete({{ $item->id }})" onclick="confirm('Are you sure you want to delete this item?') || event.stopImmediatePropagation()">
                                                            <em class="icon ni ni-trash-fill"></em><span>Delete</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    @empty
                        <tr class="nk-tb-item">
                            <td colspan="5" class="nk-tb-col text-center py-4 text-muted">No Pros & Cons found. Add one or paste JSON content.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($items->hasPages())
            <div class="card-inner border-top">
                <div class="nk-block-between-md g-3">
                    <div class="g">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Add/Edit Modal -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editingId ? 'Edit Pro/Con' : 'Add Pro/Con' }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" wire:model="category_id">
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->categoryTranslations->first()->name ?? $cat->categoryTranslations->first()->title ?? 'Category #'.$cat->id }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" wire:model="type">
                                <option value="pro">Pro (+)</option>
                                <option value="con">Con (-)</option>
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Text <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('text') is-invalid @enderror" wire:model="text" placeholder="e.g. Beginner-friendly website setup">
                            @error('text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" wire:model="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="save">Save</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Import JSON Modal (Text Area & Copy Sample) -->
    @if($showJsonModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Paste & Import JSON for Pros & Cons</h5>
                        <button type="button" class="btn-close" wire:click="closeJsonModal"></button>
                    </div>
                    <div class="modal-body">
                        @if($jsonUploadMessage)
                            <div class="alert alert-success mb-3">{{ $jsonUploadMessage }}</div>
                        @endif
                        @if($jsonUploadError)
                            <div class="alert alert-danger mb-3">{{ $jsonUploadError }}</div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-bold">Target Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('jsonCategoryId') is-invalid @enderror" wire:model="jsonCategoryId">
                                <option value="">Select Category for these Pros & Cons</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->categoryTranslations->first()->name ?? $cat->categoryTranslations->first()->title ?? 'Category #'.$cat->id }}</option>
                                @endforeach
                            </select>
                            @error('jsonCategoryId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Paste JSON Output <span class="text-danger">*</span></label>
                            <textarea class="form-control font-monospace @error('jsonText') is-invalid @enderror" 
                                      wire:model="jsonText" 
                                      rows="8" 
                                      placeholder="Paste raw JSON content or AI-generated output here..."></textarea>
                            @error('jsonText') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Copy Sample JSON Box -->
                        <div class="bg-light p-3 rounded border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="fw-bold text-dark">Clean Sample JSON Structure (Copy for AI prompt reference):</small>
                                <button type="button" class="btn btn-xs btn-outline-primary" onclick="copySampleJson(this)">
                                    <em class="icon ni ni-copy me-1"></em> Copy Sample JSON
                                </button>
                            </div>
                            <pre id="sampleJsonText" class="m-0 text-dark" style="font-size: 11px; background: #ffffff; padding: 10px; border-radius: 4px; border: 1px solid #cbd5e1; max-height: 160px; overflow-y: auto;">
[
  {
    "type": "pro",
    "text": "Beginner-friendly website and WordPress setup"
  },
  {
    "type": "con",
    "text": "Promotional prices increase upon renewal"
  }
]
                            </pre>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeJsonModal">Close</button>
                        <button type="button" class="btn btn-primary" wire:click="importJsonText" wire:loading.attr="disabled">
                            <span wire:loading.remove><em class="icon ni ni-check-circle me-1"></em> Import JSON</span>
                            <span wire:loading>Importing...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        function copySampleJson(btn) {
            const textElement = document.getElementById('sampleJsonText');
            if (!textElement) return;
            const text = textElement.innerText;
            navigator.clipboard.writeText(text).then(function() {
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<em class="icon ni ni-check me-1"></em> Copied!';
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-success', 'text-white');
                setTimeout(function() {
                    btn.innerHTML = originalHtml;
                    btn.classList.remove('btn-success', 'text-white');
                    btn.classList.add('btn-outline-primary');
                }, 2000);
            }).catch(function(err) {
                alert('Copy failed: ' + err);
            });
        }
    </script>
</div>
