@extends('admin_layout.master')
@section('content')
    {{-- <style>
        .ck.ck-content {
            min-height: 10rem;
        }

        .nk-add-product.toggle-slide.toggle-slide-right.toggle-screen-any.content-active {
            width: 40%;
        }
    </style> --}}

    <div class="nk-block nk-block-lg features">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Features</h3>
                </div>
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em
                                class="icon ni ni-more-v"></em></a>
                        <div class="toggle-expand-content" data-content="pageMenu">
                            <ul class="nk-block-tools g-3">
                                <li class="nk-block-tools-opt">
                                    <a href="{{ route('features.create') }}"
                                        class="btn btn-icon btn-primary d-md-none"><em class="icon ni ni-plus"></em></a>
                                        @if(getCurrentLanguageID() === 1)
                                        <a href="{{ route('features.create') }}"
                                        class="btn btn-primary d-none d-md-inline-flex btn-localio"><span>Add Feature</span></a>
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
                            <th class="nk-tb-col"><span class="sub-text">Category</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                            <th class="nk-tb-col tb-tnx-action"><span>Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($features->isNotEmpty())
                            @foreach ($features as $feature)
                                <tr class="nk-tb-item">
                                    {{-- Feature Name --}}
                                    <td class="nk-tb-col">
                                        <div class="user-info">
                                            <span class="tb-lead">
                                                {{ optional($feature->translations->first())->name ?? 'No name' }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Category --}}
                                    <td class="nk-tb-col">
                                        @if ($feature->category && $feature->category->translations)
                                            {{ $feature->category->translations->name }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="nk-tb-col">
                                        <span class="{{ strtolower($feature->status) === 'active' ? 'text-success' : 'text-danger' }}">
                                            {{ ucfirst($feature->status) }}
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="nk-tb-col nk-tb-col-tools">
                                        <ul class="nk-tb-actions gx-1">
                                            <li>
                                                <div class="drodown">
                                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                        data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                    <div class="dropdown-menu dropdown-menu-end" style="height: auto">
                                                        <ul class="link-list-opt no-bdr">
                                                            <li>
                                                                <a href="{{ route('features.edit', $feature->id) }}">
                                                                    <em class="icon ni ni-edit-fill"></em>
                                                                    <span>Edit</span>
                                                                </a>
                                                            </li>
                                                            <li class="removeConfermation"
                                                                data-url="{{ route('features.delete', $feature->id) }}">
                                                                <a class="delete" href="{{ route('features.delete', $feature->id) }}">
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
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
