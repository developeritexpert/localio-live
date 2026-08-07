@extends('admin_layout.master')
@section('content')
    <?php $locale = getCurrentLocale(); ?>
    <div class="nk-block nk-block-lg all-products">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Starting Prices</h3>
                    <div class="nk-block-des text-soft">
                        <p>Manage starting prices and country availability for linked businesses.</p>
                    </div>
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
                                        <em class="icon ni ni-plus me-1"></em><span>Add Starting Price</span>
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
                <table class="datatable-init nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col"><span class="sub-text">Linked Business</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Starting Price</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Active Countries/Regions</span></th>
                            <th class="nk-tb-col tb-col-md"><span class="sub-text">Category</span></th>
                            <th class="nk-tb-col nk-tb-col-tools text-end"><span class="sub-text">Actions</span></th>
                        </tr>
                    </thead>
                    @if (isset($products))
                        <tbody>
                            @foreach ($products as $product)
                                <tr class="nk-tb-item">
                                    {{-- Linked Business --}}
                                    <td class="nk-tb-col">
                                        <div class="user-card">
                                            <div class="user-avatar sm bg-primary-dim text-primary">
                                                <em class="icon ni ni-building"></em>
                                            </div>
                                            <div class="user-info">
                                                <span class="tb-lead fw-bold">
                                                    @if (isset($product->businesses) && count($product->businesses) > 0)
                                                        @foreach ($product->businesses as $business)
                                                            {{ $business->translations->first()->name ?? 'Business #' . $business->id }}
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted fw-normal">No business</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Starting Price --}}
                                    <td class="nk-tb-col">
                                        @if ($product->prices && $product->prices->isNotEmpty())
                                            @php $price = $product->prices->first(); @endphp
                                            <span class="tb-amount text-dark fw-bold">
                                                {{ number_format($price->price, 2) }} {{ $price->currency }}
                                                <span class="sub-text text-muted d-inline fw-normal">/ {{ str_replace('_', ' ', ucfirst($price->time_unit)) }}</span>
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Active Countries/Regions --}}
                                    <td class="nk-tb-col">
                                        @if ($product->active_all_countries)
                                            <span class="badge badge-dim bg-outline-primary">
                                                <em class="icon ni ni-globe me-1"></em> All Countries/Regions
                                            </span>
                                        @elseif ($product->countries && $product->countries->isNotEmpty())
                                            @php
                                                $countryCount = $product->countries->count();
                                            @endphp
                                            @if ($countryCount <= 3)
                                                @foreach ($product->countries as $country)
                                                    <span class="badge badge-dim bg-outline-info me-1 mb-1">{{ $country->name }}</span>
                                                @endforeach
                                            @else
                                                @foreach ($product->countries->take(2) as $country)
                                                    <span class="badge badge-dim bg-outline-info me-1 mb-1">{{ $country->name }}</span>
                                                @endforeach
                                                <span class="badge badge-dim bg-light text-soft mb-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $product->countries->pluck('name')->implode(', ') }}">
                                                    +{{ $countryCount - 2 }} more
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge badge-dim bg-light text-muted">None</span>
                                        @endif
                                    </td>

                                    {{-- Category --}}
                                    <td class="nk-tb-col tb-col-md">
                                        <span class="sub-text">
                                            @if (isset($product->categories) && count($product->categories) > 0)
                                                @foreach ($product->categories as $category)
                                                    {{ $category->name }}{{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                            @else
                                                <span class="text-muted">No category</span>
                                            @endif
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="nk-tb-col nk-tb-col-tools text-end">
                                        <ul class="nk-tb-actions gx-1 my-n1 justify-content-end">
                                            <li>
                                                <div class="dropdown">
                                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                                        <em class="icon ni ni-more-h"></em>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <ul class="link-list-opt no-bdr">
                                                            <li>
                                                                <a href="{{ route('product-edit', $product->id) }}">
                                                                    <em class="icon ni ni-edit-fill"></em>
                                                                    <span>Edit</span>
                                                                </a>
                                                            </li>
                                                            <li class="removeConfermation" data-url="{{ route('product-remove', $product->id) }}">
                                                                <a href="{{ route('product-remove', $product->id) }}">
                                                                    <em class="icon ni ni-trash-fill text-danger"></em>
                                                                    <span class="text-danger">Delete</span>
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
                    @endif
                </table>
            </div>
        </div>
    </div>
@endsection
