<div class="nk-block nk-block-lg">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="nk-block-title page-title mb-0">Manage FAQs</h3>
            <span class="text-muted">{{ $business->translations->first()?->name ?? 'Business' }}</span>
        </div>

        {{-- Language Switcher --}}
        <div class="form-group position-relative mb-0" style="min-width: 220px;">
            <div class="position-relative">
                <select class="form-control pe-5" wire:model.live="faqLangId">
                    @foreach ($languages as $language)
                        <option value="{{ $language->id }}">{{ $language->name }}</option>
                    @endforeach
                </select>
                <i class="fa fa-chevron-down position-absolute"
                    style="right: 15px; top: 50%; transform: translateY(-50%); pointer-events: none;"></i>
            </div>
        </div>
    </div>

    {{-- Success Message --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Add/Edit FAQ Form --}}
    <div class="card card-bordered mb-4">
        <div class="card-header bg-light">
            <h4 class="card-title">
                {{ $editingFAQId !== null ? 'Edit FAQ' : 'Add New FAQ' }}
            </h4>
        </div>
        <div class="card-inner">
            <form wire:submit.prevent="{{ $editingFAQId !== null ? 'updateFAQ' : 'addFAQ' }}">
                <div class="row g-gs">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label" for="faqQuestion">Question</label>
                            <input type="text" class="form-control @error('faqQuestion') is-invalid @enderror"
                                wire:model="faqQuestion" id="faqQuestion" placeholder="Enter FAQ question">
                            @error('faqQuestion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label" for="faqAnswer">Answer</label>
                            <textarea class="form-control @error('faqAnswer') is-invalid @enderror"
                                wire:model="faqAnswer" id="faqAnswer" rows="4"
                                placeholder="Enter FAQ answer"></textarea>
                            @error('faqAnswer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-localio">
                            <span>{{ $editingFAQId !== null ? 'Update FAQ' : 'Save FAQ' }}</span>
                        </button>

                        @if($editingFAQId !== null)
                            <button type="button" class="btn btn-secondary ms-2" wire:click="cancelFAQEdit">
                                <span>Cancel</span>
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- FAQ List --}}
    <div class="card card-bordered">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Business FAQs</h4>
            <span class="badge badge-light">{{ count($businessFAQs) }} FAQ(s)</span>
        </div>

        @if(count($businessFAQs) > 0)
            <div class="card-inner">
                <table class="table nk-tb-list nk-tb-ulist">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col">Position</th>
                            <th class="nk-tb-col">Question</th>
                            <th class="nk-tb-col">Answer</th>
                            <th class="nk-tb-col tb-tnx-action">Action</th>
                        </tr>
                    </thead>
                    <tbody class="sortable-tbody" id="faq-sortable">
                        @foreach($businessFAQs as $faq)
                            <tr class="nk-tb-item faq-row" data-faq-id="{{ $faq['id'] }}" data-position="{{ $faq['position'] }}" draggable="true">
                                <td class="nk-tb-col">
                                    <div class="d-flex align-items-center">
                                        <div class="move-indicator me-2">⋮⋮</div>
                                        <span class="position-badge">#{{ $faq['position'] }}</span>
                                    </div>
                                </td>
                                <td class="nk-tb-col">
                                    <span class="tb-lead">{{ $faq['question'] ?: '(no translation yet)' }}</span>
                                    @if(!$faq['status'])
                                        <span class="badge badge-dim badge-warning ms-1">Inactive</span>
                                    @endif
                                </td>
                                <td class="nk-tb-col">
                                    <span class="tb-sub">{{ Str::limit(strip_tags($faq['answer']), 100) }}</span>
                                </td>
                                <td class="nk-tb-col nk-tb-col-tools">
                                    <ul class="nk-tb-actions gx-1">
                                        <li><a wire:click="editFAQ({{ $faq['id'] }})" style="cursor:pointer;"><em class="icon ni ni-edit-fill"></em></a></li>
                                        <li><a wire:click="toggleFAQStatus({{ $faq['id'] }})" style="cursor:pointer;"><em class="icon ni ni-eye{{ $faq['status'] ? '-off' : '' }}-fill"></em></a></li>
                                        <li><a wire:click="deleteFAQ({{ $faq['id'] }})" style="cursor:pointer;" onclick="return confirm('Delete this FAQ?')"><em class="icon ni ni-trash-fill"></em></a></li>
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="card-inner text-center">
                <h5>No FAQs Added Yet</h5>
                <p class="text-muted">Add your first FAQ using the form above.</p>
            </div>
        @endif
    </div>
</div>