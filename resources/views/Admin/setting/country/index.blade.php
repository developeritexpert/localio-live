{{-- {{ dd($countries) }} --}}
@extends('admin_layout.master')
@section('content')

<div class="nk-block nk-block-lg countries-list">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Country</h3>
            </div>
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                        <em class="icon ni ni-more-v"></em>
                    </a>
                    <div class="toggle-expand-content" data-content="pageMenu">
                        <ul class="nk-block-tools g-3">
                            <li class="nk-block-tools-opt">
                                <a href="{{ url('admin-dashboard/site-languages/add') }}" class="btn btn-icon btn-primary d-md-none">
                                    <em class="icon ni ni-plus"></em>
                                </a>
                                <a href="{{ route('country.add') }}" class="btn btn-primary d-none d-md-inline-flex btn-localio">
                                    <span>Add Country</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <table class="datatable-init nowrap nk-tb-list nk-tb-ulist" data-auto-responsive="false" id="CountryDataTable">
                <thead>
                    <tr class="nk-tb-item nk-tb-head">
                        <th class="nk-tb-col"><span class="sub-text">Country Name</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Country Code</span></th>
                        <th class="nk-tb-col tb-tnx-action"><span>Action</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($countries as $country)
                    <tr class="nk-tb-item">
                        <td class="nk-tb-col">
                            <span class="tb-lead">{{ $country->name }}</span>
                        </td>

                        <td class="nk-tb-col">
                            <span class="tb-lead">{{ $country->country_code }}</span>
                        </td>

                        <td class="nk-tb-col nk-tb-col-tools">
                            <ul class="nk-tb-actions gx-1">
                                <li>
                                    <div class="drodown">
                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                            <em class="icon ni ni-more-h"></em>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end edit-btn" style="height: auto !important;">
                                            <ul class="link-list-opt no-bdr">
                                                <li>
                                                    <a href="{{ route('country.update', ['id' => $country->id]) }}">
                                                        <em class="icon ni ni-edit-fill"></em>
                                                        <span>Edit</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('country.delete', ['id' => $country->id]) }}">
                                                        <em class="icon ni ni-trash-fill"></em>
                                                        <span>Delete</span>
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
    </div>
</div>

@endsection
