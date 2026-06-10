@extends('admin_layout.master')
@section('content')

<div class="nk-block nk-block-lg vendor-register">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Vendor Registration Requests</h3>
            </div>
        </div>
    </div>

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            {{-- Only show pending requests --}}
            @php
                $pendingVendors = $vendors->where('status', 'pending');
            @endphp

            @if($pendingVendors->isNotEmpty())
                <table class="datatable-init nowrap nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col">#</th>
                            <th class="nk-tb-col">First Name</th>
                            <th class="nk-tb-col">Last Name</th>
                            <th class="nk-tb-col">Email</th>
                            <th class="nk-tb-col">Business Name</th>
                            <th class="nk-tb-col">Status</th>
                            <th class="nk-tb-col tb-tnx-action">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingVendors as $vendor)
                            <tr class="nk-tb-item">
                                <td class="nk-tb-col">{{ $loop->iteration }}</td>
                                <td class="nk-tb-col">{{ $vendor->first_name ?? 'N/A' }}</td>
                                <td class="nk-tb-col">{{ $vendor->last_name ?? 'N/A' }}</td>
                                <td class="nk-tb-col">{{ $vendor->email ?? 'N/A' }}</td>
                                <td class="nk-tb-col">
                                    @if($vendor->business && $vendor->business->translations)
                                        {{ $vendor->business->translations->first()->name }}
                                    @else
                                        <span class="text-danger">Business not found</span>
                                    @endif
                                </td>
                                <td class="nk-tb-col">
                                    <span class="badge bg-warning">Pending</span>
                                </td>
                                <td class="nk-tb-col nk-tb-col-tools">
                                    <ul class="nk-tb-actions gx-1">
                                        <li>
                                            <div class="drodown">
                                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                                    <em class="icon ni ni-more-h"></em>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <ul class="link-list-opt no-bdr">
                                                        <li>
                                                            <a href="{{ route('admin-vendor-registration.handle', ['id' => $vendor->id, 'action' => 'approve']) }}">
                                                                <em class="icon ni ni-check-circle-fill"></em> Approve
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ route('admin-vendor-registration.handle', ['id' => $vendor->id, 'action' => 'reject']) }}">
                                                                <em class="icon ni ni-cross-circle-fill"></em> Reject
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
            @else
                <div class="text-center">
                    <button class="btn btn-primary">No Pending Vendor Requests Found</button>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
