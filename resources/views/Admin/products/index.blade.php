@extends('admin_layout.master')
@section('content')
    <?php $locale = getCurrentLocale(); ?>
    <div class="nk-block nk-block-lg all-products">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Products</h3>
                </div>
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                            <em class="icon ni ni-more-v"></em>
                        </a>
                        <div class="toggle-expand-content" data-content="pageMenu">
                            <ul class="nk-block-tools g-3">
                                <li class="nk-block-tools-opt">
                                    <a href="{{ route('product-add') }}" class="btn btn-icon btn-primary d-md-none">
                                        <em class="icon ni ni-plus"></em>
                                    </a>
                                    @if(getCurrentLanguageID() === 1)
                                    <a href="{{ route('product-add') }}" class="btn btn-primary d-none d-md-inline-flex">
                                        <span>Add Product</span>
                                    </a>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <table class="datatable-init nowrap nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col"><span class="sub-text">Name</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Linked Business</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Product Category</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Product Link</span></th>
                            <th class="nk-tb-col tb-tnx-action"><span>Action</span></th>
                        </tr>
                    </thead>
                    @if (isset($products))
                        <tbody>
                            @foreach ($products as $product)
                                <tr class="nk-tb-item">
                                    <td class="nk-tb-col">
                                        <div class="user-card">
                                            <div class="user-info">
                                                <span class="tb-lead">{{ $product->name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="nk-tb-col tb-col-mb">
                                        <span class="tb-lead">
                                            @if (isset($product->businesses) && count($product->businesses) > 0)
                                            @foreach ($product->businesses as $business)
                                                    {{ $business->translations->first()->name }}
                                                    @endforeach
                                            @else
                                                No data found
                                            @endif
                                        </span>
                                    </td>
                                    <td class="nk-tb-col tb-col-mb">
                                        <span class="tb-lead">
                                            @if (isset($product->categories) && count($product->categories) > 0)
                                                @foreach ($product->categories as $category)
                                                    {{ $category->name }}{{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                            @else
                                                No data found
                                            @endif
                                        </span>
                                    </td>
                                    <td class="nk-tb-col tb-col-mb">
                                        <span class="tb-lead">
                                            <a href="{{ $product->product_link ?? '#' }}" target="_blank">Product</a>
                                        </span>
                                    </td>
                                    <td class="nk-tb-col nk-tb-col-tools">
                                        <ul class="nk-tb-actions gx-1">
                                            <li>
                                                <div class="drodown">
                                                <div class="dropdown" style="display: inline-block; position: relative;">
                                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                        data-bs-toggle="dropdown" aria-expanded="false"
                                                        style="background: none; border: none; padding: 5px;">
                                                        <em class="icon ni ni-more-h"></em>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end"
                                                        style="border-radius: 8px; height: auto; padding: 5px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);">
                                                        <ul class="link-list-opt no-bdr"
                                                            style="list-style: none; padding: 0; margin: 0;">
                                                            <li>
                                                                <a href="{{ route('product-edit', $product->id) }}"
                                                                    style="display: flex; align-items: center; gap: 5px; padding: 8px; text-decoration: none; color: #333;">
                                                                    <em class="icon ni ni-edit-fill"
                                                                        ></em>
                                                                    <span>Edit</span>
                                                                </a>
                                                            </li>
                                                            <li class="removeConfermation"
                                                            data-url="{{ route('product-remove', $product->id) }}">
                                                                <a href="{{ route('product-remove', $product->id) }}"
                                                                    style="display: flex; align-items: center; gap: 5px; padding: 8px; text-decoration: none; color: #333;">
                                                                    <em class="icon ni ni-trash-fill"></em>
                                                                    <span>Delete</span>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    @endif
                </table>
            </div>
        </div>
    </div>
@endsection
