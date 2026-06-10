@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg all-users">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Users</h3>
            </div>
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                        <em class="icon ni ni-more-v"></em>
                    </a>
                    <div class="toggle-expand-content" data-content="pageMenu">
                        <ul class="nk-block-tools g-3">
                            <li class="nk-block-tools-opt">
                                <a href="{{ route('admin-add-user') }}" class="btn btn-icon btn-primary d-md-none">
                                    <em class="icon ni ni-plus"></em>
                                </a>
                                <a href="{{ route('admin-add-user') }}" class="btn btn-primary d-none d-md-inline-flex">
                                    <span>Add User</span>
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
        @if(isset($users) && $users->isNotEmpty())
        <table class="datatable-init nowrap nk-tb-list nk-tb-ulist" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col"><span class="sub-text">First Name</span></th>
                    <th class="nk-tb-col"><span class="sub-text">Last Name</span></th>
                    <th class="nk-tb-col"><span class="sub-text">Email</span></th>
                    <th class="nk-tb-col"><span class="sub-text">status</span></th>
                    <th class="nk-tb-col"><span class="sub-text">Joined Date</span></th>

                    <th class="nk-tb-col tb-tnx-action">
                        <span>Action</span>
                    </th>
                </tr>
            </thead>

            <tbody>
                @foreach($users as $user)
                    <tr class="nk-tb-item">
                        <td class="nk-tb-col">
                            <div class="user-card">
                                <div class="user-info">
                                    <span
                                        class="tb-lead">{{  $user->first_name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="nk-tb-col">
                            <div class="user-card">
                                <div class="user-info">
                                    <span class="tb-lead">
                                        {{ $user->last_name ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </td>

                         <td class="nk-tb-col">
                            <div class="user-card">
                                <div class="user-info">
                                    <span
                                        class="tb-lead">{{ $user->email ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="nk-tb-col">
                            <div class="user-card">
                                <div class="user-info">
                                    <span class="tb-lead">
                                        {{ $user->status === 'pending' ? 'Deactivated' : ucfirst($user->status) }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="nk-tb-col">
                            <div class="user-card">
                                <div class="user-info">
                                    <span
                                        class="tb-lead">{{$user->created_at->format('Y-m-d') ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="nk-tb-col nk-tb-col-tools">
                            <ul class="nk-tb-actions gx-1">
                                <li>
                                    <div class="drodown">
                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                            data-bs-toggle="dropdown"><em
                                                class="icon ni ni-more-h"></em></a>
                                        <div class="dropdown-menu dropdown-menu-end edit-btn"
                                            style="height: auto !important;">
                                            <ul class="link-list-opt no-bdr">
                                                @if ($user->status === 'active')
                                                <li>
                                                    <a
                                                        href="{{ route('admin-user-status-update',$user->id) }}">
                                                        <em class="icon ni ni-cross-circle-fill"></em>
                                                        <span>Deactivate
                                                    </a>
                                                </li>
                                            @else
                                                <li>
                                                    <a
                                                        href="{{ route('admin-user-status-update',$user->id) }}">
                                                        <em class="icon ni ni-check-circle-fill"></em>
                                                        <span>Activate</span>
                                                    </a>
                                                </li>
                                            @endif
                                                <li>
                                                    <a href="{{ route('admin-edit-user',['id'=>$user->id]) }}">
                                                        <em class="icon ni ni-edit-fill"></em></em>Edit
                                                    </a>
                                                </li>
                                                <li class="removeConfermation"
                                                data-url="{{ route('admin-delete-user', $user->id) }}">
                                                    <a href="{{ route('admin-delete-user', $user->id) }}">
                                                        <em class="icon ni ni-trash-fill"></em>Delete
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
@else
<div class="text-center">
    <button class="btn btn-primary btn-localio">No USer found</button>
</div>
@endif


@endsection
