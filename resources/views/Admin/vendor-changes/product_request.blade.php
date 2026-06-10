@extends('admin_layout.master')
@section('content')

<div class="nk-block nk-block-lg product-change-request">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Product Change Requests</h3>
            </div>
        </div>
    </div>

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            @if(isset($productRequests) && $productRequests->isNotEmpty())
                <div class="table-responsive">
                    <table class="datatable-init nowrap nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                        <thead>
                            <tr class="nk-tb-item nk-tb-head">
                                <th class="nk-tb-col"><span class="sub-text" style="font-weight: 600;">Product Name</span></th>
                                <th class="nk-tb-col"><span class="sub-text" style="font-weight: 600;">Product Link</span></th>
                                {{-- <th class="nk-tb-col"><span class="sub-text" style="font-weight: 600;">Affiliate</span></th> --}}
                                <th class="nk-tb-col"><span class="sub-text" style="font-weight: 600;">Request For</span></th>

                                <th class="nk-tb-col"><span class="sub-text" style="font-weight: 600;">Price Details</span></th>
                                <th class="nk-tb-col"><span class="sub-text" style="font-weight: 600;">Requested By</span></th>
                                <th class="nk-tb-col"><span class="sub-text" style="font-weight: 600;">Status</span></th>
                                <th class="nk-tb-col"><span class="sub-text" style="font-weight: 600;">Requested At</span></th>
                                <th class="nk-tb-col"><span class="sub-text" style="font-weight: 600;">Action</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productRequests as $request)
                            @php
                                $value = json_decode($request->value, true);
                            @endphp

                                <tr class="nk-tb-item" style="border-bottom: 1px solid #e3e7f3;">
                                    <td class="nk-tb-col" style="vertical-align: top; padding: 16px 12px;">
                                        <div style="font-weight: 600; color: #364a63; margin-bottom: 4px;">
                                            {{ $value['name'] ?? 'N/A' }}
                                        </div>
                                        <small class="text-muted">Product Request</small>
                                    </td>

                                    <td class="nk-tb-col" style="vertical-align: top; padding: 16px 12px;">
                                        <div style="max-width: 200px;">
                                            <a href="{{ $value['link'] }}" target="_blank" class="btn btn-sm btn-outline-primary" style="margin-bottom: 8px;">
                                                View Product
                                            </a>
                                            <div style="font-size: 11px; color: #8094ae; word-break: break-all;">
                                                {{ Str::limit($value['link'], 50) }}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- <td class="nk-tb-col" style="text-align: center; vertical-align: top; padding: 16px 12px;">
                                        @if(!empty($value['is_affiliate']))
                                            <span class="badge badge-success"style="color:#000">Yes</span>
                                        @else
                                            <span class="badge badge-warning" style="color:#000">No</span>
                                        @endif
                                    </td>
                                     --}}

                                     <td class="nk-tb-col" style="text-align: center; vertical-align: top; padding: 16px 12px;">
                                        @if(!empty($value['product_id']))
                                            <span class="badge badge-success"style="color:#000">Edit Product</span>
                                        @else
                                            <span class="badge badge-warning" style="color:#000">New Product</span>
                                        @endif
                                    </td>


                                    <td class="nk-tb-col" style="vertical-align: top; padding: 16px 12px;">
                                        <div style="background: #f5f6fa; padding: 12px; border-radius: 6px; border: 1px solid #e3e7f3;">
                                            <div style="margin-bottom: 6px;">
                                                <strong style="color: #364a63;">{{ $value['price']['price'] ?? '-' }}</strong>
                                                <small class="text-muted">{{ $value['price']['currency'] ?? '' }} / {{ $value['price']['time_unit'] ?? '' }}</small>
                                            </div>

                                            @if(!empty($value['price']['additional_info']))
                                                <div style="font-size: 12px; color: #8094ae; margin-bottom: 6px;">
                                                    {{ $value['price']['additional_info'] }}
                                                </div>
                                            @endif

                                            @if (!empty($value['price']['discount_price']))
                                                <div style="background: #e8f5e8; padding: 6px 8px; border-radius: 4px; margin-bottom: 6px; border: 1px solid #c3e6c3;">
                                                    <div style="font-size: 12px; color: #0fa500; font-weight: 600;">
                                                        Discount: {{ $value['price']['discount_price'] }}
                                                    </div>
                                                    <div style="font-size: 11px; color: #0fa500;">
                                                        Until: {{ $value['price']['discount_expiration_date'] ?? '-' }}
                                                    </div>
                                                </div>
                                            @endif

                                            @if (!empty($value['price']['renewal_price']))
                                                <div style="background: #fff3cd; padding: 6px 8px; border-radius: 4px; border: 1px solid #ffeaa7;">
                                                    <div style="font-size: 12px; color: #856404; font-weight: 600;">
                                                        Renewal: {{ $value['price']['renewal_price'] }}
                                                    </div>
                                                    <div style="font-size: 11px; color: #856404;">
                                                        ({{ $value['price']['renewal_time_unit'] ?? '-' }})
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="nk-tb-col" style="vertical-align: top; padding: 16px 12px;">
                                        <div style="font-weight: 600; color: #364a63;">
                                            {{ $request->user->first_name ?? 'System' }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $request->user ? 'User Request' : 'System Request' }}
                                        </small>
                                    </td>

                                    <td class="nk-tb-col" style="text-align: center; vertical-align: top; padding: 16px 12px;">
                                        <span class="badge bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'approved' ? 'success' : 'danger') }}" style="font-size: 11px; padding: 6px 12px;">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>

                                    <td class="nk-tb-col" style="vertical-align: top; padding: 16px 12px;">
                                        <div style="font-weight: 600; color: #364a63;">
                                            {{ $request->created_at->format('M d, Y') }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $request->created_at->format('h:i A') }}
                                        </small>
                                    </td>

                                    <td class="nk-tb-col nk-tb-col-tools" style="vertical-align: top; padding: 16px 12px;">
                                        <ul class="">
                                            <li>
                                                <div class="drodown">
                                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                                        <em class="icon ni ni-more-h"></em>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end" style="height: auto;">
                                                        <ul class="link-list-opt no-bdr">
                                                            <li style="padding: 4px 0;">
                                                                <a href="{{ route('admin-product-change.handle', ['id' => $request->id, 'action' => 'approve']) }}" style="padding: 8px 16px; display: flex; align-items: center; gap: 8px;">
                                                                    <em class="icon ni ni-check-circle" style="color: #28a745;"></em>
                                                                    <span>Approve</span>
                                                                </a>
                                                            </li>
                                                            <li style="padding: 4px 0;">
                                                                <a href="{{ route('admin-product-change.handle', ['id' => $request->id, 'action' => 'reject']) }}" style="padding: 8px 16px; display: flex; align-items: center; gap: 8px;">
                                                                    <em class="icon ni ni-cross-circle" style="color: #dc3545;"></em>
                                                                    <span>Reject</span>
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
            @else
            <div class="text-center">
                <button class="btn btn-primary btn-localio">No Change Requests Found</button>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
