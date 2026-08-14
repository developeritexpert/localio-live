@extends('admin_layout.master')
@section('content')

<div class="nk-block nk-block-lg">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Base Languages</h3>
                <div class="nk-block-des text-soft">
                    <p>Manage standardized base languages with Google Cloud Translate codes and language combination tags.</p>
                </div>
            </div>
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em
                            class="icon ni ni-more-v"></em></a>
                    <div class="toggle-expand-content" data-content="pageMenu">
                        <ul class="nk-block-tools g-3">
                            <li class="nk-block-tools-opt">
                                <a href="{{ route('base-languages.add') }}"
                                   class="btn btn-primary d-none d-md-inline-flex btn-localio"><em
                                        class="icon ni ni-plus"></em><span>Add Base Language</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <table class="datatable-init nowrap nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                <thead>
                    <tr class="nk-tb-item nk-tb-head">
                        <th class="nk-tb-col"><span class="sub-text">Name</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Google Translate Code</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Language Tag</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Master</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                        <th class="nk-tb-col tb-tnx-action">
                            <span>Action</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($baseLanguages as $lang)
                        <tr class="nk-tb-item">
                            <td class="nk-tb-col">
                                <div class="user-card">
                                    <div class="user-info">
                                        <span class="tb-lead">{{ $lang->name }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="nk-tb-col tb-col-mb">
                                <span class="badge bg-outline-info">{{ $lang->code }}</span>
                            </td>
                            <td class="nk-tb-col tb-col-md">
                                <span class="badge bg-outline-secondary">{{ $lang->language_tag }}</span>
                            </td>
                            <td class="nk-tb-col tb-col-md">
                                @if($lang->is_master)
                                    <span class="badge bg-primary">Master Language</span>
                                @else
                                    <span class="text-soft">-</span>
                                @endif
                            </td>
                            <td class="nk-tb-col tb-col-md">
                                @if($lang->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="nk-tb-col nk-tb-col-tools">
                                <ul class="nk-tb-actions gx-1">
                                    <li>
                                        <div class="drodown">
                                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                            <div class="dropdown-menu dropdown-menu-end edit-btn" style="height: 94px !important;">
                                                <ul class="link-list-opt no-bdr">
                                                    <li>
                                                        <a href="{{ route('base-languages.update', $lang->id) }}">
                                                            <em class="icon ni ni-edit-fill"></em>
                                                            <span>Edit</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('base-languages.toggle-status', $lang->id) }}">
                                                            <em class="icon ni ni-{{ $lang->status ? 'cross' : 'check' }}-circle-fill"></em>
                                                            <span>{{ $lang->status ? 'Disable' : 'Enable' }}</span>
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
