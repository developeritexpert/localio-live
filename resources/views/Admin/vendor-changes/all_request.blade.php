@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg business-change-request">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Business Change Requests</h3>
            </div>
        </div>
    </div>

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            @if(isset($changeRequests) && $changeRequests->isNotEmpty())
                <table class="datatable-init nowrap nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col"><span class="sub-text">Business Name</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Field</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Type</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Action Type</span></th>
                            {{-- <th class="nk-tb-col"><span class="sub-text">New Value</span></th> --}}
                            <th class="nk-tb-col"><span class="sub-text">Requested By</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Requested At</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Action</span></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($changeRequests as $request)
                            <tr class="nk-tb-item">
                                <td class="nk-tb-col">
                                    {{ $request->business->translations->first()->name ?? 'N/A' }}
                                </td>

                                <td class="nk-tb-col">
                                    {{ ucfirst(str_replace('_', ' ', $request->field)) }}
                                </td>

                                <td class="nk-tb-col">
                                    {{ ucfirst($request->type) }}
                                </td>

                                <td class="nk-tb-col">
                                    @php
                                        $type = $request->action_type ?? 'N/A';
                                        $badgeClass = match ($type) {
                                            'add' => 'bg-success',
                                            'delete' => 'bg-danger',
                                            'replace', 'update' => 'bg-warning text-dark',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} text-capitalize">
                                        {{ $type }}
                                    </span>
                                </td>

                                {{-- Business Image --}}
                                {{-- <td class="nk-tb-col">
                                    @if($request->field === 'icon_id')
                                        <img src="{{ asset($request->value) }}"
                                             alt="Icon"
                                             width="50"
                                             height="50"
                                             style="object-fit: contain; border-radius: 6px;">
                                    @elseif($request->field === 'business_images')
                                        @php $images = json_decode($request->value, true);


                                        @endphp
                                        @if(is_array($images) && count($images))
                                            <div class="d-flex flex-wrap">
                                                @foreach($images as $img)
                                                    <img src="{{ asset($img) }}"
                                                         alt="Image"
                                                         width="60"
                                                         height="60"
                                                         style="object-fit: cover; border-radius: 6px;">
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">No images</span>
                                        @endif
                                    @else
                                        {{ Str::limit(strip_tags($request->value), 60) }}
                                    @endif
                                </td> --}}

                                <td class="nk-tb-col">
                                    {{ $request->user->first_name ?? 'System' }}
                                </td>

                                <td class="nk-tb-col">
                                    <span class="badge bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'approved' ? 'success' : 'danger') }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>

                                <td class="nk-tb-col">
                                    {{ $request->created_at->format('Y-m-d') }}
                                </td>

                                <td class="nk-tb-col nk-tb-col-tools">
                                    <ul class="">
                                        <li>
                                            <div class="drodown">
                                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                                    <em class="icon ni ni-more-h"></em>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end" style="height: auto;">
                                                    <ul class="link-list-opt no-bdr">

                                                        <li>
                                                            <a onclick="viewChanges({{ $request->id }})">
                                                                <em class="icon ni ni-eye"></em> <span>View Changes</span>
                                                            </a>
                                                        </li>

                                                        {{-- <li>
                                                            <a href="{{ route('admin-vendor-change.handle', ['id' => $request->id, 'action' => 'approve']) }}">
                                                                <em class="icon ni ni-check-circle"></em> <span>Approve</span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ route('admin-vendor-change.handle', ['id' => $request->id, 'action' => 'reject']) }}">
                                                                <em class="icon ni ni-cross-circle"></em> <span>Reject</span>
                                                            </a>
                                                        </li> --}}
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
            @else
                <div class="text-center">
                    <button class="btn btn-primary btn-localio">No Change Requests Found</button>
                </div>
            @endif
        </div>
    </div>

    <!-- Change Review Modal -->
    <div class="modal fade" id="changeReviewModal" tabindex="-1" aria-labelledby="changeReviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="changeReviewModalLabel">
                        <em class="icon ni ni-swap-alt me-2"></em>
                        Review Change Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <!-- Info Summary -->
                    <div class="row gx-3 gy-2 mb-4 p-3 bg-light border-start border-4 border-primary rounded">
                        <div class="col-md-3">
                            <strong>Business:</strong>
                            <div id="businessName">-</div>
                        </div>
                        <div class="col-md-3">
                            <strong>Field:</strong>
                            <div id="fieldName">-</div>
                        </div>
                        <div class="col-md-3">
                            <strong>Action:</strong>
                            <div id="actionType">-</div>
                        </div>
                        <div class="col-md-3">
                            <strong>Requested By:</strong>
                            <div id="requestedBy">-</div>
                        </div>
                    </div>

                    <!-- Comparison -->
                    <div class="border rounded overflow-hidden">
                        <div class="row g-0">
                            <!-- Current Value -->
                            <div class="col-md-6 border-end">
                                <div class="p-4 bg-light">
                                    <div class="badge bg-danger-subtle text-danger fw-semibold mb-3 text-uppercase">
                                        <em class="icon ni ni-history me-1"></em>
                                        Current Value
                                    </div>
                                    <div id="currentValue" class="small" style="line-height: 1.6; word-wrap: break-word;">
                                        <!-- Current value here -->
                                    </div>
                                </div>
                            </div>

                            <!-- New Value -->
                            <div class="col-md-6">
                                <div class="p-4 bg-success-subtle">
                                    <div class="badge bg-success text-white fw-semibold mb-3 text-uppercase">
                                        <em class="icon ni ni-arrow-right me-1"></em>
                                        <span id="changeTypeLabel">New Value</span>
                                    </div>
                                    <div id="newValue" class="small" style="line-height: 1.6; word-wrap: break-word;">
                                        <!-- New value here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                            <em class="icon ni ni-cross me-1"></em> Cancel
                        </button>
                        <button type="button" class="btn btn-danger me-2" onclick="handleAction('reject')">
                            <em class="icon ni ni-cross-circle me-1"></em> Reject
                        </button>
                        <button type="button" class="btn btn-success" onclick="handleAction('approve')">
                            <em class="icon ni ni-check-circle me-1"></em> Approve
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

@endsection

<script>
    let currentRequestData = null;
const requestsData = @json($changeRequests).map(request => ({
    id: request.id,
    business_name: request.business?.translations?.[0]?.name || 'N/A',
    field: request.field,
    action_type: request.action_type,
    current_value: getCurrentValue(request),
    new_value: parseValue(request.value),
    requested_by: request.user?.first_name || 'System'
}));

function parseValue(value) {
    try {
        return JSON.parse(value);
    } catch (e) {
        return value; // fallback if not JSON
    }
}

function getCurrentValue(request) {
    switch (request.field) {
        case 'business_images':
            return request.business?.business_images || [];
        case 'icon_id':
            return request.business?.icon_id || '';
        case 'description':
        case 'name':
            return request.business?.translations?.[0]?.[request.field] || '';
        default:
            return '';
    }
}

function viewChanges(requestId) {
    const request = requestsData.find(r => r.id === requestId);
    if (!request) return;

    currentRequestData = request;

    document.getElementById('businessName').textContent = request.business_name;
    document.getElementById('fieldName').textContent = formatFieldName(request.field);
    document.getElementById('actionType').innerHTML = `<span class="badge bg-info text-capitalize">${request.action_type}</span>`;
    document.getElementById('requestedBy').textContent = request.requested_by;

    updateChangeTypeLabel(request.action_type);
    renderComparison(request);
    $('#changeReviewModal').modal('show');
}

function updateChangeTypeLabel(action) {
    const label = document.getElementById('changeTypeLabel');
    const map = {
        add: 'Added Images',
        update: 'Updated Value',
        replace: 'Replaced Images',
        delete: 'Deleted Images'
    };

    const iconMap = {
        add: 'plus-circle',
        update: 'edit',
        replace: 'swap-alt',
        delete: 'trash'
    };

    label.innerHTML = `<em class="icon ni ni-${iconMap[action]} me-1"></em>${map[action] || 'Change'}`;
}

function renderComparison(data) {
    const currentEl = document.getElementById('currentValue');
    const newEl = document.getElementById('newValue');

    if (data.field === 'business_images' || data.field === 'icon_id') {
        renderImageComparison(currentEl, newEl, data);
    } else {
        renderTextComparison(currentEl, newEl, data);
    }
}

function renderImageComparison(currentEl, newEl, data) {
    const currentImages = Array.isArray(data.current_value) ? data.current_value : [];
    const changeData = data.new_value;

    currentEl.innerHTML = '';
    newEl.innerHTML = '';

    if (data.action_type === 'replace') {
        // changeData format: {index: {old: "path", new: "newpath"}}

        // Show current images with highlights
        currentImages.forEach((img, index) => {
            if (!img) return;

            const isBeingReplaced = changeData.hasOwnProperty(index.toString());
            const badgeHtml = isBeingReplaced
                ? `<span class="badge bg-warning position-absolute top-0 end-0">WILL BE REPLACED</span>`
                : '';

            currentEl.innerHTML += `
                <div class="position-relative d-inline-block me-2 mb-2">
                    <img src="/${img}" width="80" height="80" class="rounded border ${isBeingReplaced ? 'opacity-75' : ''}">
                    <small class="position-absolute bottom-0 start-0 bg-dark text-white px-1" style="font-size: 10px;">Index: ${index}</small>
                    ${badgeHtml}
                </div>`;
        });

        // Show replacement images
        Object.entries(changeData).forEach(([index, replaceInfo]) => {
            if (!replaceInfo.new) return;

            newEl.innerHTML += `
                <div class="position-relative d-inline-block me-2 mb-2">
                    <img src="/${replaceInfo.new}" width="80" height="80" class="rounded border">
                    <small class="position-absolute bottom-0 start-0 bg-success text-white px-1" style="font-size: 10px;">Index: ${index}</small>
                    <span class="badge bg-success position-absolute top-0 end-0">REPLACEMENT</span>
                </div>`;
        });

    } else if (data.action_type === 'delete') {
        // changeData format: {index: "path_to_delete"}

        // Show current images with delete highlights
        currentImages.forEach((img, index) => {
            if (!img) return;

            const isBeingDeleted = changeData.hasOwnProperty(index.toString());
            const badgeHtml = isBeingDeleted
                ? `<span class="badge bg-danger position-absolute top-0 end-0">WILL BE DELETED</span>`
                : '';

            currentEl.innerHTML += `
                <div class="position-relative d-inline-block me-2 mb-2">
                    <img src="/${img}" width="80" height="80" class="rounded border ${isBeingDeleted ? 'opacity-50' : ''}">
                    <small class="position-absolute bottom-0 start-0 bg-dark text-white px-1" style="font-size: 10px;">Index: ${index}</small>
                    ${badgeHtml}
                </div>`;
        });

        // Show what will be deleted
        Object.entries(changeData).forEach(([index, imagePath]) => {
            newEl.innerHTML += `
                <div class="position-relative d-inline-block me-2 mb-2">
                    <img src="/${imagePath}" width="80" height="80" class="rounded border opacity-50">
                    <small class="position-absolute bottom-0 start-0 bg-danger text-white px-1" style="font-size: 10px;">Index: ${index}</small>
                    <span class="badge bg-danger position-absolute top-0 end-0">DELETED</span>
                    <div class="position-absolute top-50 start-50 translate-middle">
                        <i class="fas fa-times text-danger" style="font-size: 24px;"></i>
                    </div>
                </div>`;
        });

    } else if (data.action_type === 'add') {
        // changeData format: ["path1", "path2", ...]

        // Show current images
        currentImages.forEach((img, index) => {
            if (!img) return;
            currentEl.innerHTML += `
                <div class="position-relative d-inline-block me-2 mb-2">
                    <img src="/${img}" width="80" height="80" class="rounded border">
                    <small class="position-absolute bottom-0 start-0 bg-dark text-white px-1" style="font-size: 10px;">Index: ${index}</small>
                </div>`;
        });

        // Show new images to be added
        if (Array.isArray(changeData)) {
            changeData.forEach((newImg, addIndex) => {
                if (!newImg) return;
                const newIndex = currentImages.length + addIndex; // Calculate where it will be added

                newEl.innerHTML += `
                    <div class="position-relative d-inline-block me-2 mb-2">
                        <img src="/${newImg}" width="80" height="80" class="rounded border">
                        <small class="position-absolute bottom-0 start-0 bg-success text-white px-1" style="font-size: 10px;">New Index: ${newIndex}</small>
                        <span class="badge bg-success position-absolute top-0 end-0">ADDED</span>
                    </div>`;
            });
        }

    } else {
        // Handle single icon or other cases
        const current = Array.isArray(data.current_value) ? data.current_value : [data.current_value];
        const updated = Array.isArray(data.new_value) ? data.new_value : [data.new_value];

        current.forEach(img => {
            if (!img) return;
            currentEl.innerHTML += `
                <div class="position-relative d-inline-block me-2 mb-2">
                    <img src="/${img}" width="80" height="80" class="rounded border">
                </div>`;
        });

        updated.forEach(img => {
            if (!img) return;
            newEl.innerHTML += `
                <div class="position-relative d-inline-block me-2 mb-2">
                    <img src="/${img}" width="80" height="80" class="rounded border">
                    <span class="badge bg-info position-absolute top-0 end-0">UPDATED</span>
                </div>`;
        });
    }

    // Add empty state messages
    if (currentEl.innerHTML.trim() === '') {
        currentEl.innerHTML = '<div class="text-muted p-3"><em>No current images</em></div>';
    }

    if (newEl.innerHTML.trim() === '') {
        newEl.innerHTML = '<div class="text-muted p-3"><em>No changes</em></div>';
    }
}

function renderTextComparison(currentEl, newEl, data) {
    currentEl.innerHTML = `<div class="p-3 bg-light border rounded">${data.current_value || '<em>No current value</em>'}</div>`;
    newEl.innerHTML = `<div class="p-3 bg-light border rounded position-relative">
        ${data.new_value || '<em>No new value</em>'}
        <span class="badge bg-info position-absolute top-0 end-0">${data.action_type.toUpperCase()}</span>
    </div>`;
}

function formatFieldName(field) {
    return field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
}

function handleAction(action) {
    if (!currentRequestData) return;
    window.location.href = `{{ route('admin-vendor-change.handle', ['id' => '__ID__', 'action' => '__ACTION__']) }}`
        .replace('__ID__', currentRequestData.id)
        .replace('__ACTION__', action);
}
</script>
