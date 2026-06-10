@extends('admin_layout.master')
@section('content')

    <?php
    $locale = getCurrentLocale();
    ?>
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ ucfirst(str_replace('_', ' ', request('tab'))) }}</h3>
                </div>
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em
                                class="icon ni ni-more-v"></em></a>
                        <div class="toggle-expand-content" data-content="pageMenu">
                            <ul class="nk-block-tools g-3">
                                <li class="nk-block-tools-opt">
                                    <a href="{{ route('productfeature.add', ['tab' => request('tab')]) }}"
                                        class=" btn btn-icon btn-primary d-md-none"><em class="icon ni ni-plus"></em></a>
                                    <a href="{{ route('productfeature.add', ['tab' => request('tab')]) }}"
                                        class=" btn btn-primary d-none d-md-inline-flex"><em
                                            class=""></em><span>Add Feature</span></a>
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
                            <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                            <th class="nk-tb-col tb-tnx-action">
                                <span>Action</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($features))
                            @foreach ($features as $value)
                            <tr class="nk-tb-item">
                                <td class="nk-tb-col">
                                    <div class="user-card">
                                        <div class="user-info">
                                            <span class="tb-lead">
                                                {{ $value['feature_name'] }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="nk-tb-col">
                                    <div class="user-card">
                                        <div class="user-info">
                                            <span class="tb-lead">
                                                {{ $value['status'] }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="nk-tb-col">
                                    <div class="drodown">
                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                            data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                        <div class="dropdown-menu dropdown-menu-end edit-btn"
                                            style="height: 68px !important">
                                            <ul class="link-list-opt no-bdr">
                                                <li><a
                                                        href="{{ route('productfeature.update', ['id' => $value['feature_id'], 'tab' => request('tab')]) }}"><em
                                                            class="icon ni ni-edit-fill"></em><span>Edit</span></a>
                                                </li>
                                                <li class="removeConfermation"
                                                            data-url="{{ route('productfeature.remove-process', ['id' => $value['feature_id'], 'tab' => request('tab')])  }}">
                                                    <a
                                                        href="{{ route('productfeature.remove-process', ['id' => $value['feature_id'], 'tab' => request('tab')]) }}"><em
                                                            class="icon ni ni-trash-fill"></em><span>Remove</span></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>

                </table>
            </div>
        </div>
    </div>
@endsection
