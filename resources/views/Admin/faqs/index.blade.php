@extends('admin_layout.master')
@section('content')

<?php $locale = getCurrentLocale(); ?>

<div class="nk-block-head nk-block-head-sm faqs-section">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">FAQs</h3>
        </div>
        <div class="nk-block-head-content">
            <div class="toggle-wrap nk-block-tools-toggle">
                <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                    <em class="icon ni ni-more-v"></em>
                </a>
                <div class="toggle-expand-content" data-content="pageMenu">
                    <ul class="nk-block-tools g-3">
                        <li class="nk-block-tools-opt">
                            <a href="{{ route('faq-add') }}" class="btn btn-primary d-none d-md-inline-flex btn-localio">
                                <span>Add FAQ</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- title and decription --}}

 {{-- <div class="card card-bordered mb-4">
    <div class="card-header bg-light">
        <h5 class="card-title mb-0">Getting Started</h5> <!-- Static Title -->
    </div>
    <div class="card-inner" style="max-height: 400px; overflow-y: auto;">
        <table class="table nk-tb-list nk-tb-ulist">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col">Title</th>
                    <th class="nk-tb-col">Description</th>
                    <th class="nk-tb-col tb-tnx-action">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr class="nk-tb-item">
                    <td class="nk-tb-col">Account Setup</td> <!-- Static FAQ Title -->
                    <td class="nk-tb-col">Instructions on how to set up your account.</td> <!-- Static FAQ Description -->
                    <td class="nk-tb-col nk-tb-col-tools">
                        <div class="drodown">
                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                <em class="icon ni ni-more-h"></em>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <ul class="link-list-opt no-bdr">
                                    <li><a href="#"><em class="icon ni ni-edit-fill"></em><span>Update</span></a></li>
                                    <li><a href="#"><em class="icon ni ni-trash-fill"></em><span>Remove</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div> --}}


<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="card card-bordered mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Getting Started</h5>
        {{-- <button class="btn btn-sm btn-secondary toggle-faq-edit">Edit</button> --}}
    </div>
    <div class="card-inner" style="max-height: 400px; overflow-y: auto;">
        <table class="table nk-tb-list nk-tb-ulist mb-0">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col">Title</th>
                    <th class="nk-tb-col">Description</th>
                    <th class="nk-tb-col tb-tnx-action">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr class="nk-tb-item">
                    <!-- Display Mode -->
                    <td class="nk-tb-col display-faq-title">
                        {{ $faq_title?->default_value ?? '' }}
                    </td>
                    <td class="nk-tb-col display-faq-description">
                        {{ $faq_description?->default_value ?? '' }}
                    </td>
                    <td class="nk-tb-col nk-tb-col-tools display-faq-action">
                        <button class="btn btn-sm btn-secondary toggle-faq-edit">Edit</button>
                    </td>

                    <!-- Edit Mode (Hidden by Default) -->
                    <td class="nk-tb-col edit-faq-title d-none">
                        <input type="text" class="form-control faq-title" value="{{ $faq_title?->default_value ?? '' }}">
                    </td>
                    <td class="nk-tb-col edit-faq-description d-none">
                        <textarea class="form-control faq-description" rows="2">{{ $faq_description?->default_value ?? '' }}</textarea>
                    </td>
                    <td class="nk-tb-col nk-tb-col-tools edit-faq-action d-none">
                        <button class="btn btn-sm btn-primary update-faq-inline">Update</button>
                    </td>

                </tr>
            </tbody>
        </table>
    </div>
</div>



@foreach (['vendor' => 'Vendor FAQs', 'user' => 'User FAQs'] as $type => $title)
    @if (!empty($groupedFaqs[$type]))
        <div class="mb-4">
            <h4 class="mb-3">{{ $title }}</h4>

            @foreach ($groupedFaqs[$type] as $category => $faqsInCategory)
                <div class="card card-bordered mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">{{ $category }}</h5>
                    </div>
                    <div class="card-inner" style="max-height: 400px; overflow-y: auto;">
                        <table class="table nk-tb-list nk-tb-ulist">
                            <thead>
                                <tr class="nk-tb-item nk-tb-head">
                                    <th class="nk-tb-col">Question</th>
                                    <th class="nk-tb-col">Answer</th>
                                    <th class="nk-tb-col tb-tnx-action">Action</th>
                                </tr>
                            </thead>
                            <tbody class="sortable-tbody" data-category="{{ Str::slug($category) }}">
                                @foreach ($faqsInCategory as $index => $faq)
                                    <tr class="nk-tb-item faq-row" data-faq-id="{{ $faq->id }}" data-position="{{ $faq->position ?? $index + 1 }}" draggable="true">
                                        <td class="nk-tb-col">
                                            <div class="move-indicator">⋮⋮</div>
                                            <span class="position-badge">#{{ $faq->position ?? $index + 1 }}</span>
                                            {{ $faq->translations->first()?->question ?? $faq->question }}
                                        </td>
                                        <td class="nk-tb-col">
                                            {{ strip_tags($faq->translations->first()?->answer ?? $faq->answer) }}
                                        </td>
                                        <td class="nk-tb-col nk-tb-col-tools">
                                            <div class="drodown">
                                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                                    <em class="icon ni ni-more-h"></em>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end" style="list-style: none; padding: 0; margin: 0; height:auto">
                                                    <ul class="link-list-opt no-bdr">
                                                        <li>
                                                            <a href="{{ route('faq-edit', $faq->id) }}">
                                                                <em class="icon ni ni-edit-fill"></em><span>Edit</span>
                                                            </a>
                                                        </li>
                                                        <li class="removeConfermation" data-url="{{ route('remove-faq', $faq->id) }}">
                                                            <a href="#">
                                                                <em class="icon ni ni-trash-fill"></em><span>Remove</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endforeach

@endsection



    <script>
        class FAQReorderManager {
            constructor() {
                this.draggedElement = null;
                this.draggedOver = null;
                this.init();
            }

            init() {
                this.setupEventListeners();
                this.setupDropdowns();
            }

            setupEventListeners() {
                document.querySelectorAll('.faq-row').forEach(row => {
                    row.addEventListener('dragstart', this.handleDragStart.bind(this));
                    row.addEventListener('dragend', this.handleDragEnd.bind(this));
                    row.addEventListener('dragover', this.handleDragOver.bind(this));
                    row.addEventListener('dragenter', this.handleDragEnter.bind(this));
                    row.addEventListener('dragleave', this.handleDragLeave.bind(this));
                    row.addEventListener('drop', this.handleDrop.bind(this));
                });
            }

            // setupDropdowns() {
            //     document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
            //         toggle.addEventListener('click', (e) => {
            //             e.preventDefault();
            //             e.stopPropagation();

            //             // Close all other dropdowns
            //             document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
            //                 if (menu !== toggle.nextElementSibling) {
            //                     menu.classList.remove('show');
            //                 }
            //             });

            //             // Toggle current dropdown
            //             const menu = toggle.nextElementSibling;
            //             menu.classList.toggle('show');
            //         });
            //     });

            //     // Close dropdowns when clicking outside
            //     document.addEventListener('click', () => {
            //         document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
            //             menu.classList.remove('show');
            //         });
            //     });
            // }

            handleDragStart(e) {
                this.draggedElement = e.target.closest('.faq-row');
                this.draggedElement.classList.add('dragging');

                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/html', this.draggedElement.outerHTML);
            }

            handleDragEnd(e) {
                if (this.draggedElement) {
                    this.draggedElement.classList.remove('dragging');
                }

                document.querySelectorAll('.faq-row').forEach(row => {
                    row.classList.remove('drag-over', 'drag-over-bottom');
                });

                this.draggedElement = null;
                this.draggedOver = null;
            }

            handleDragOver(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
            }

            handleDragEnter(e) {
                e.preventDefault();
                const targetRow = e.target.closest('.faq-row');

                if (targetRow && targetRow !== this.draggedElement) {
                    this.draggedOver = targetRow;

                    document.querySelectorAll('.faq-row').forEach(row => {
                        row.classList.remove('drag-over', 'drag-over-bottom');
                    });

                    const rect = targetRow.getBoundingClientRect();
                    const mouseY = e.clientY;
                    const middle = rect.top + rect.height / 2;

                    if (mouseY < middle) {
                        targetRow.classList.add('drag-over');
                    } else {
                        targetRow.classList.add('drag-over-bottom');
                    }
                }
            }

            handleDragLeave(e) {
                const targetRow = e.target.closest('.faq-row');
                if (targetRow) {
                    targetRow.classList.remove('drag-over', 'drag-over-bottom');
                }
            }

            handleDrop(e) {
                e.preventDefault();

                if (!this.draggedElement || !this.draggedOver) return;

                const targetRow = this.draggedOver;
                const tbody = targetRow.closest('tbody');

                const rect = targetRow.getBoundingClientRect();
                const mouseY = e.clientY;
                const middle = rect.top + rect.height / 2;

                if (mouseY < middle) {
                    tbody.insertBefore(this.draggedElement, targetRow);
                } else {
                    tbody.insertBefore(this.draggedElement, targetRow.nextSibling);
                }

                this.updatePositions(tbody);
                this.saveOrder(tbody);

                document.querySelectorAll('.faq-row').forEach(row => {
                    row.classList.remove('drag-over', 'drag-over-bottom', 'dragging');
                });
            }

            updatePositions(tbody) {
                const rows = tbody.querySelectorAll('.faq-row');
                rows.forEach((row, index) => {
                    const position = index + 1;
                    row.setAttribute('data-position', position);

                    const badge = row.querySelector('.position-badge');
                    if (badge) {
                        badge.textContent = `#${position}`;
                    }
                });
            }

            saveOrder(tbody) {
                const category = tbody.getAttribute('data-category');
                const faqIds = [];

                tbody.querySelectorAll('.faq-row').forEach(row => {
                    faqIds.push({
                        id: row.getAttribute('data-faq-id'),
                        position: row.getAttribute('data-position')
                    });
                });

                this.sendUpdateRequest(category, faqIds);
            }

            sendUpdateRequest(category, faqIds) {
                // CSRF token for Laravel
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch('{{ route("faqs.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        category: category,
                        faqs: faqIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.showToast('FAQ order updated successfully');
                    } else {
                        this.showToast('Failed to update FAQ order', 'error');
                        // Optionally reload the page to restore original order
                        // location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.showToast('An error occurred while updating', 'error');
                    // Optionally reload the page to restore original order
                    // location.reload();
                });
            }

            showToast(message, type = 'info') {
            const event = new CustomEvent('show-toast', {
                detail: {
                    message: message,
                    type: type,
                }
            });
            window.dispatchEvent(event);
            }
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new FAQReorderManager();
        });
    </script>


<!-- Update FAQ JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButton = document.querySelector('.toggle-faq-edit');
        const updateButton = document.querySelector('.update-faq-inline');

        const displayTitle = document.querySelector('.display-faq-title');
        const displayDescription = document.querySelector('.display-faq-description');
        const displayAction = document.querySelector('.display-faq-action');

        const editTitle = document.querySelector('.edit-faq-title');
        const editDescription = document.querySelector('.edit-faq-description');
        const editAction = document.querySelector('.edit-faq-action');

        // Toggle to edit mode
        toggleButton.addEventListener('click', function () {
            // Hide display view, show edit view
            displayTitle.classList.add('d-none');
            displayDescription.classList.add('d-none');
            displayAction.classList.add('d-none');

            editTitle.classList.remove('d-none');
            editDescription.classList.remove('d-none');
            editAction.classList.remove('d-none');
        });

        // Submit updated data
        updateButton.addEventListener('click', function () {
            const title = document.querySelector('.faq-title').value;
            const description = document.querySelector('.faq-description').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch("{{ route('faqs.line.update') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    title: title,
                    description: description
                })
            })
            .then(response => response.json())
            .then(data => {
                toastr.clear();
                if (data.success) {
                    NioApp.Toast('FAQ updated successfully.', 'success', {
                        position: 'top-right'
                    });

                    // Update displayed values
                    displayTitle.textContent = title;
                    displayDescription.textContent = description;

                    // Switch back to display mode
                    editTitle.classList.add('d-none');
                    editDescription.classList.add('d-none');
                    editAction.classList.add('d-none');

                    displayTitle.classList.remove('d-none');
                    displayDescription.classList.remove('d-none');
                    displayAction.classList.remove('d-none');
                } else {
                    NioApp.Toast(data.message || 'Update failed.', 'error', {
                        position: 'top-right'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.clear();
                NioApp.Toast('Something went wrong. Please try again.', 'error', {
                    position: 'top-right'
                });
            });
        });
    });
</script>
