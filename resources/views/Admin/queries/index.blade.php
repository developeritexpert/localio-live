@extends('admin_layout.master')

@section('content')
<style>
    /* Query Action Dropdown Fix */

    .nk-tb-actions .dropdown-menu {
        min-width: 180px !important;
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
    }

    .nk-tb-actions .dropdown-menu .link-list-opt {
        display: block !important;
    }

    .nk-tb-actions .dropdown-menu .link-list-opt li {
        display: block !important;
        width: 100%;
    }

    .nk-tb-actions .dropdown-menu .link-list-opt li a {
        display: flex !important;
        align-items: center;
        gap: 8px;
        padding: 10px 15px;
        white-space: nowrap;
    }

    .nk-tb-actions .dropdown-menu.show {
        overflow: visible !important;
    }
</style>

<div class="nk-block nk-block-lg all-users">

<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Queries</h3>
        </div>
    </div>
</div>

<div class="card card-bordered card-preview">
    <div class="card-inner">

        @if(isset($queries) && $queries->isNotEmpty())

        <table class="datatable-init nowrap nk-tb-list nk-tb-ulist" data-auto-responsive="false">
            <thead>
                <tr class="nk-tb-item nk-tb-head">
                    <th class="nk-tb-col"><span class="sub-text">Query Type</span></th>
                    <th class="nk-tb-col"><span class="sub-text">Name</span></th>
                    <th class="nk-tb-col"><span class="sub-text">Email</span></th>
                    <th class="nk-tb-col"><span class="sub-text">Message</span></th>
                    <th class="nk-tb-col"><span class="sub-text">Date</span></th>
                    <th class="nk-tb-col tb-tnx-action">
                        <span>Action</span>
                    </th>
                </tr>
            </thead>

            <tbody>
                @foreach($queries as $query)

                <tr class="nk-tb-item">

                    <td class="nk-tb-col">
                        <span class="tb-lead">
                            @switch($query->query_type)
                                @case(1)
                                    General Inquiry
                                    @break
                                @case(2)
                                    Support
                                    @break
                                @case(3)
                                    Feedback
                                    @break
                                @case(4)
                                    Business Collaboration
                                    @break
                                @default
                                    N/A
                            @endswitch
                        </span>
                    </td>

                    <td class="nk-tb-col">
                        <span class="tb-lead">
                            {{ $query->name ?? 'N/A' }}
                        </span>
                    </td>

                    <td class="nk-tb-col">
                        <span class="tb-lead">
                            {{ $query->email ?? 'N/A' }}
                        </span>
                    </td>

                    <td class="nk-tb-col">
                        <span class="tb-lead">
                            {{ \Illuminate\Support\Str::limit($query->message, 50) }}
                        </span>
                    </td>

                    <td class="nk-tb-col">
                        <span class="tb-lead">
                            {{ $query->created_at->format('Y-m-d') }}
                        </span>
                    </td>

                    <td class="nk-tb-col nk-tb-col-tools">
                        <ul class="nk-tb-actions gx-1">
                            <li>
                                <div class="dropdown">
                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                       data-bs-toggle="dropdown">
                                        <em class="icon ni ni-more-h"></em>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end edit-btn">
                                        <ul class="link-list-opt no-bdr">

                                            <li>
                                                <a href="{{ route('admin.queries.show', $query->id) }}">
                                                    <em class="icon ni ni-eye-fill"></em>
                                                    <span>View</span>
                                                </a>
                                            </li>

                                            <li>
                                                <a href="mailto:{{ $query->email }}">
                                                    <em class="icon ni ni-mail-fill"></em>
                                                    <span>Email Reply</span>
                                                </a>
                                            </li>

                                            <li class="removeConfermation"
                                                data-url="{{ route('admin.queries.delete', $query->id) }}">
                                                <a href="{{ route('admin.queries.delete', $query->id) }}">
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

        @else

        <div class="text-center">
            <button class="btn btn-primary btn-localio">
                No Queries Found
            </button>
        </div>

        @endif

    </div>
</div>

</div>
@endsection
