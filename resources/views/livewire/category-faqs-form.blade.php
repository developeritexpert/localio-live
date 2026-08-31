<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- JSON FAQ Upload --}}
    <div class="card card-bordered mb-4">
        <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between" data-bs-toggle="collapse" data-bs-target="#jsonUploadBlock" style="cursor: pointer;">
            <h5 class="card-title mb-0 text-white"><em class="icon ni ni-code"></em> JSON FAQ Upload <span class="badge bg-white text-primary ms-2">Bulk add FAQs easily</span></h5>
            <em class="icon ni ni-chevron-down"></em>
        </div>
        <div id="jsonUploadBlock" class="collapse show" wire:ignore.self>
            <div class="card-inner bg-light">
                <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center">
                    <em class="icon ni ni-info me-2 text-info"></em>
                    <small>How to use: Paste a JSON array containing FAQ objects below. New categories will be automatically added if they don't already exist.</small>
                </div>
                
                <button type="button" class="btn btn-sm btn-outline-primary bg-white mb-2" onclick="copyFAQJsonTemplate()">
                    Copy JSON Template
                </button>
                <small class="ms-2 text-muted">Copies an example JSON array format to clipboard</small>

                <div class="form-group mt-2">
                    <label class="form-label">Paste FAQ JSON Array below:</label>
                    <textarea class="form-control" wire:model="json_data" rows="5" placeholder='[{"category": "General", "question": "Question 1?", "answer": "Answer 1"}, {"category": "Pricing", "question": "Question 2?", "answer": "Answer 2"}]'></textarea>
                </div>
                
                <div class="d-flex mt-3 gap-2">
                    <button class="btn btn-primary btn-localio" wire:click="uploadJsonFaqs" wire:loading.attr="disabled">
                        <em class="icon ni ni-upload-cloud"></em> Upload JSON FAQs
                    </button>
                    <button class="btn btn-outline-light bg-white" wire:click="$set('json_data', '')">Clear</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Category FAQ Categories --}}
    <div class="card card-bordered mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><em class="icon ni ni-tags me-1"></em> Category FAQ Categories</h5>
            <span class="badge bg-primary">{{ count($faqCategories) }} Categories</span>
        </div>
        <div class="card-inner">
            <div class="row g-3 align-items-end mb-3">
                <div class="col-md-8 col-sm-12">
                    <label class="form-label" for="newCategoryName">New Category Name</label>
                    <input type="text" class="form-control" id="newCategoryName" wire:model="newCategoryName" placeholder="e.g. Freelancer, Pricing, Services, General">
                    @error('newCategoryName') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-4 col-sm-12">
                    <button type="button" class="btn btn-primary btn-localio w-100" wire:click="addFaqCategory" wire:loading.attr="disabled">
                        <em class="icon ni ni-plus"></em> + Add Category
                    </button>
                </div>
            </div>

            @if(count($faqCategories) > 0)
                <div class="d-flex flex-wrap gap-2 pt-2 border-top mt-3">
                    @foreach($faqCategories as $index => $cat)
                        <div class="badge bg-light text-dark border p-2 d-flex align-items-center gap-2">
                            <span class="fw-bold">{{ $cat }}</span>
                            <button type="button" class="btn btn-xs btn-outline-danger p-0 border-0" wire:click="deleteFaqCategory({{ $index }})" title="Delete Category" onclick="confirm('Are you sure you want to delete this category? FAQs in it will default to General.') || event.stopImmediatePropagation()">
                                <em class="icon ni ni-cross-sm"></em>
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted small mb-0 mt-3 pt-2 border-top">No categories created yet. Adding categories allows group-based FAQ tabs on the frontend.</p>
            @endif
        </div>
    </div>

    {{-- Add/Edit FAQ Form --}}
    <div class="card card-bordered mb-4">
        <div class="card-header bg-light">
            <h4 class="card-title">
                {{ $editingFAQIndex !== null ? 'Edit FAQ' : 'Add New FAQ' }}
            </h4>
        </div>
        <div class="card-inner">
            <form wire:submit.prevent="{{ $editingFAQIndex !== null ? 'updateFAQ' : 'addFAQ' }}">
                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label">FAQ Category (Optional)</label>
                        <select class="form-select" wire:model="faqCategoryId">
                            <option value="">-- General / Uncategorized --</option>
                            @foreach($faqCategories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Question</label>
                        <input type="text" class="form-control" wire:model="faqQuestion" placeholder="Enter FAQ question">
                        @error('faqQuestion') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-12" wire:ignore>
                        <label class="form-label">Answer</label>
                        <textarea class="form-control" id="faqAnswerEditor" rows="4" placeholder="Enter FAQ answer"></textarea>
                    </div>
                    @error('faqAnswer') <div class="col-md-12"><span class="text-danger small">{{ $message }}</span></div> @enderror

                    <div class="col-md-12 mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-localio" wire:loading.attr="disabled">
                            <em class="icon ni ni-save"></em> 
                            <span>{{ $editingFAQIndex !== null ? 'Update FAQ' : 'Save FAQ' }}</span>
                        </button>
                        @if($editingFAQIndex !== null)
                            <button type="button" class="btn btn-outline-light bg-white" wire:click="resetFAQForm" wire:loading.attr="disabled">Cancel</button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- FAQs List --}}
    <div class="card card-bordered">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Category FAQs</h5>
            <span class="badge bg-secondary">{{ count($faqs) }} FAQs</span>
        </div>
        <div class="card-inner p-0">
            @if(count($faqs) > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="15%">Category</th>
                                <th width="35%">Question</th>
                                <th width="35%">Answer</th>
                                <th width="15%" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($faqs as $index => $faq)
                                <tr>
                                    <td><span class="badge bg-primary">{{ $faq['category'] ?? 'General' }}</span></td>
                                    <td class="fw-bold">{{ \Illuminate\Support\Str::limit($faq['question'], 50) }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit(strip_tags($faq['answer']), 50) }}</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-icon btn-primary" wire:click="editFAQ({{ $index }})" title="Edit FAQ">
                                            <em class="icon ni ni-edit"></em>
                                        </button>
                                        <button class="btn btn-sm btn-icon btn-danger ms-1" wire:click="deleteFAQ({{ $index }})" onclick="confirm('Are you sure you want to delete this FAQ?') || event.stopImmediatePropagation()" title="Delete FAQ">
                                            <em class="icon ni ni-trash"></em>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center p-5 text-muted">
                    <em class="icon ni ni-help-fill" style="font-size: 3rem; color: #e2e8f0;"></em>
                    <h5 class="mt-2 text-secondary">No FAQs added yet</h5>
                    <p>Use the form above to add your first FAQ, or upload a JSON array.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyFAQJsonTemplate() {
        const tmpl = `[
    {
        "category": "General",
        "question": "What is included in the basic package?",
        "answer": "<p>Our basic package includes everything you need to get started.</p>"
    }
]`;
        navigator.clipboard.writeText(tmpl).then(function() {
            NioApp.Toast('Example JSON array copied to clipboard!', 'info', { position: 'top-right' });
        });
    }

    document.addEventListener('livewire:initialized', () => {
        let editorInstance = null;
        if (typeof ClassicEditor !== 'undefined') {
            ClassicEditor
                .create(document.querySelector('#faqAnswerEditor'))
                .then(editor => {
                    editorInstance = editor;
                    editor.model.document.on('change:data', () => {
                        @this.set('faqAnswer', editor.getData());
                    });
                })
                .catch(error => {
                    console.error(error);
                });

            @this.on('reset-ckeditor', () => {
                if(editorInstance) {
                    editorInstance.setData('');
                }
            });

            @this.on('set-ckeditor', (data) => {
                if(editorInstance && data && data[0] && data[0].data !== undefined) {
                    editorInstance.setData(data[0].data);
                } else if(editorInstance && typeof data === 'object' && data.data !== undefined) {
                     editorInstance.setData(data.data);
                }
            });
        }
    });
</script>
@endpush
