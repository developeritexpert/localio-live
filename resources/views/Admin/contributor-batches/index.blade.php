@extends('admin_layout.master')

@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Contributor Batches</h3>
                </div>

                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#"
                           class="btn btn-icon btn-trigger toggle-expand me-n1"
                           data-target="pageMenu">
                            <em class="icon ni ni-more-v"></em>
                        </a>

                        <div class="toggle-expand-content" data-content="pageMenu">
                            <ul class="nk-block-tools g-3">
                                <li class="nk-block-tools-opt">
                                    <a href="{{ route('contributor-batches.create') }}"
                                       class="btn btn-primary btn-localio">
                                        <em class="icon ni ni-plus"></em>
                                        <span>Add Contributor Batch</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>
            </div>
        @endif

        {{-- Error Message --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>
            </div>
        @endif

        <div class="card card-bordered card-preview">
            <div class="card-inner">

                <table class="datatable-init nowrap nk-tb-list nk-tb-ulist"
                       data-auto-responsive="false">

                    <thead>
                        <tr class="nk-tb-item nk-tb-head">

                            <th class="nk-tb-col">
                                <span class="sub-text">Badge</span>
                            </th>

                            <th class="nk-tb-col">
                                <span class="sub-text">Published Reviews</span>
                            </th>

                            <th class="nk-tb-col">
                                <span class="sub-text">Icon</span>
                            </th>

                            <th class="nk-tb-col">
                                <span class="sub-text">Status</span>
                            </th>

                            <th class="nk-tb-col tb-tnx-action">
                                <span>Action</span>
                            </th>

                        </tr>
                    </thead>

                    <tbody>

                        @forelse($contributorBatches as $batch)

                            <tr class="nk-tb-item">

                                {{-- Badge Name --}}
                                <td class="nk-tb-col">
                                    <div class="user-info">
                                        <span class="tb-lead">
                                            {{ $batch->badge_name }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Review Range --}}
                                <td class="nk-tb-col">

                                    @if($batch->max_reviews === null)

                                        <span>
                                            {{ $batch->min_reviews }}+
                                        </span>

                                    @else

                                        <span>
                                            {{ $batch->min_reviews }} - {{ $batch->max_reviews }}
                                        </span>

                                    @endif

                                </td>

                                {{-- Icon --}}
                                <td class="nk-tb-col">

                                    @if($batch->icon)

                                        <img
                                            src="{{ asset('storage/' . $batch->icon) }}"
                                            alt="{{ $batch->badge_name }}"
                                            style="
                                                width: 40px;
                                                height: 40px;
                                                object-fit: contain;
                                                border-radius: 6px;
                                            "
                                        >

                                    @else

                                        <span class="text-muted">
                                            No icon
                                        </span>

                                    @endif

                                </td>

                                {{-- Status --}}
                                <td class="nk-tb-col">

                                    @if($batch->status === 'active')

                                        <span class="text-success">
                                            Active
                                        </span>

                                    @else

                                        <span class="text-danger">
                                            Inactive
                                        </span>

                                    @endif

                                </td>

                                {{-- Actions --}}
                                <td class="nk-tb-col nk-tb-col-tools">

                                    <ul class="nk-tb-actions gx-1">

                                        <li>
                                            <div class="drodown">

                                                <a href="#"
                                                   class="dropdown-toggle btn btn-icon btn-trigger"
                                                   data-bs-toggle="dropdown">

                                                    <em class="icon ni ni-more-h"></em>

                                                </a>

                                                <div class="dropdown-menu dropdown-menu-end"
                                                     style="height: auto">

                                                    <ul class="link-list-opt no-bdr">

                                                        {{-- Edit --}}
                                                        <li>

                                                            <a href="{{ route('contributor-batches.edit', $batch->id) }}">

                                                                <em class="icon ni ni-edit-fill"></em>

                                                                <span>Edit</span>

                                                            </a>

                                                        </li>

                                                        {{-- Delete --}}
                                                        <li class="removeConfermation"
                                                            data-url="{{ route('contributor-batches.delete', $batch->id) }}">

                                                            <a class="delete"
                                                               href="{{ route('contributor-batches.delete', $batch->id) }}">

                                                                <em class="icon ni ni-trash-fill"></em>

                                                                <span>Remove</span>

                                                            </a>

                                                        </li>

                                                    </ul>

                                                </div>

                                            </div>
                                        </li>

                                    </ul>

                                </td>

                            </tr>

                        @empty

                            <tr class="nk-tb-item">

                                <td class="nk-tb-col text-center py-4"
                                    colspan="5">

                                    <span class="text-muted">
                                        No contributor batches found.
                                    </span>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>
        </div>

    </div>
@endsection