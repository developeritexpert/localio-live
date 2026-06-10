@extends('admin_layout.master')
@section('content')
    

    <div class="nk-block nk-block-lg">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ $category->translation->name }}</h3>
                </div>
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em
                                class="icon ni ni-more-v"></em></a>
                        <div class="toggle-expand-content" data-content="pageMenu">
                            <ul class="nk-block-tools g-3">
                                <li class="nk-block-tools-opt">
                                    <a href="{{ route('filter-add',$category->id)}}"
                                        class=" btn btn-primary d-none d-md-inline-flex btn-localio"><em
                                            class=""></em><span>Add Filter</span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <!-- Order update notification -->
                <div class="order-saving" id="orderSavingNotification">
                    <em class="icon ni ni-check-circle"></em> Order updated successfully
                </div>

                <table class="datatable-init nowrap nk-tb-list nk-tb-ulist" data-auto-responsive="false" id="filtersTable">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col" style="width: 40px;"></th>
                            <th class="nk-tb-col"><span class="sub-text">Filter Label</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Type</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Required </span></th>
                            <th class="nk-tb-col"><span class="sub-text">Order </span></th>
                            <th class="nk-tb-col tb-tnx-action">
                                <span>Action</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="filtersTableBody">
                        @if ($filters)
                            @foreach ($filters as $filter)
                                <tr class="nk-tb-item filter-row" data-id="{{ $filter->id }}">
                                    <td class="nk-tb-col text-center" style="width: 40px;">
                                        <div class="drag-handle" title="Drag to reorder">
                                            <span style="font-size: 20px; letter-spacing: 3px; color: #6c757d;">⋯</span>
                                        </div>
                                    </td>
                                    <td class="nk-tb-col">
                                        <div class="user-card">
                                            <div class="user-info">
                                                <span
                                                    class="tb-lead">{{ $filter->translations->isNotEmpty() ? $filter->translations->first()->name : $filter->name ?? '' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    @php
                                        $filter_type_id=App\Models\FilterType::where('id',$filter->filter_type_id)->first();
                                    @endphp
                                    <td class="nk-tb-col tb-col-mb">
                                        <span class="tb-amount">{{ $filter_type_id->name ?? 'No Type Set Yet'}}</span>
                                    </td>

                                    <td class="nk-tb-col tb-col-md">
                                        <span class="tb-amount">
                                            {{ $filter->is_required ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="nk-tb-col tb-col-md order-column">
                                        <span class="tb-amount order-value">
                                            {{ $filter->display_order }}</span>
                                    </td>
                                    <td class="nk-tb-col nk-tb-col-tools">
                                        <ul class="nk-tb-actions gx-1">
                                            <li>
                                                <div class="drodown">
                                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                        data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                    <div class="dropdown-menu dropdown-menu-end edit-btn"
                                                        style="height: 94px !important;">
                                                        <ul class="link-list-opt no-bdr">
                                                            <li>
                                                                <a
                                                                    href="{{ url('admin-dashboard/update-filter') ?? '' }}/{{ $filter->id ?? '' }}">
                                                                    <em class="icon ni ni-edit-fill"></em>
                                                                    <span>Edit</span>
                                                                </a>
                                                            </li>

                                                            <li class="removeConfermation"
                                                                data-url="{{ url('admin-dashboard/remove-filter') ?? '' }}/{{ $filter->id ?? '' }}">
                                                                <a class="delete"
                                                                    href="{{ url('admin-dashboard/remove-filter') ?? '' }}/{{ $filter->id ?? '' }}">
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
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Include SortableJS library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Disable the default DataTable initialization for this table
            if ($.fn.DataTable.isDataTable('#filtersTable')) {
                $('#filtersTable').DataTable().destroy();
            }

            // Initialize Sortable on the table body
            const sortable = new Sortable(document.getElementById('filtersTableBody'), {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: function(evt) {
                    updateFilterOrder();
                },
            });

            // Function to update the filter order after drag and drop
            function updateFilterOrder() {
                const rows = document.querySelectorAll('#filtersTableBody tr.filter-row');
                const orderData = [];

                // Collect the IDs and their new positions
                rows.forEach((row, index) => {
                    const filterId = row.getAttribute('data-id');
                    orderData.push({
                        id: filterId,
                        position: index + 1
                    });

                    // Update the displayed order number in the table
                    const orderCell = row.querySelector('.order-value');
                    if (orderCell) {
                        orderCell.textContent = index + 1;
                    }
                });

                // Send AJAX request to update order in database
                fetch('{{ route("update-filter-order") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        filters: orderData
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success notification
                        const notification = document.getElementById('orderSavingNotification');
                        notification.style.display = 'block';

                        // Hide notification after 2 seconds
                        setTimeout(() => {
                            notification.style.display = 'none';
                        }, 2000);
                    } else {
                        console.error('Error updating order:', data.message);
                        // You could show an error notification here
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    </script>
@endsection
