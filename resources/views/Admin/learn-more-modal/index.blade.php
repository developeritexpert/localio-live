@extends('admin_layout.master')
@section('content')

    {{-- <style>
        .ck.ck-content {
            min-height: 10rem;
        }
    </style> --}}

    <?php $lang = getCurrentLocale(); ?>

    <div class="nk-block nk-block-lg learn-more-model-content">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Learn More Sections</h3>
                </div>
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                            <em class="icon ni ni-more-v"></em>
                        </a>
                        <div class="toggle-expand-content" data-content="pageMenu">
                            <ul class="nk-block-tools g-3">
                                <li class="nk-block-tools-opt">
                                    @if(getCurrentLanguageID() === 1)
                                    <a href="{{ route('learn-modal-create') }}" class="btn btn-primary">
                                        <span>Add Section</span>
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
                            <th class="nk-tb-col"><span class="sub-text">Title</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Content</span></th>
                            {{-- <th class="nk-tb-col"><span class="sub-text">Sort Order</span></th> --}}
                            <th class="nk-tb-col tb-tnx-action"><span>Action</span></th>
                        </tr>
                    </thead>
                    @if (isset($sections) && $sections->isNotEmpty())
                        <tbody>
                            @foreach ($sections as $section)
                                <tr class="nk-tb-item">
                                    <td class="nk-tb-col">
                                        <span class="tb-lead">{{ $section->title ?? '-' }}</span>
                                    </td>
                                    <td class="nk-tb-col tb-col-mb">
                                        <span
                                            class="tb-lead">{{ Str::limit(strip_tags($section->content ?? '-'), 100) }}</span>
                                    </td>

                                    <td class="nk-tb-col nk-tb-col-tools">
                                        <ul class="nk-tb-actions gx-1">
                                            <li>
                                                <div class="dropdown">
                                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                        data-bs-toggle="dropdown">
                                                        <em class="icon ni ni-more-h"></em>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end" style="height:auto">
                                                        <ul class="link-list-opt no-bdr">
                                                            <li>
                                                                <a href="{{ route('edit', $section->id) }}">
                                                                    <em class="icon ni ni-edit-fill"></em>
                                                                    <span>Edit</span>
                                                                </a>
                                                            </li>
                                                            <li class="removeConfermation"
                                                            data-url="{{ route('modaldestroy', $section->id) }}">
                                                                    <a href="{{ route('modaldestroy', $section->id) }}">
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
                    @endif
                </table>
            </div>
        </div>
    </div>
@endsection
