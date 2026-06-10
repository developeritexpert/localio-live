@extends('admin_layout.master')
@section('content')

    <?php

    $lang = getCurrentLocale();

    ?>
    <div class="nk-block nk-block-lg expert-guide-category">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title"> Category</h3>
                </div>
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <div class="toggle-expand-content" data-content="pageMenu">
                            <ul class="nk-block-tools g-3">
                                <li class="nk-block-tools-opt">
                                    @if (getCurrentLanguageID() === 1)
                                        <a href="{{ route('admin-expert-guide-add-category') }}"
                                            class=" btn btn-primary d-none d-md-inline-flex"><em
                                                class=""></em><span>Add Category</span></a>
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
                            <th class="nk-tb-col"><span class="sub-text">Slug</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Description</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Image</span></th> <!-- Missing in tbody -->
                            <th class="nk-tb-col tb-tnx-action"><span>Action</span></th> <!-- Missing in tbody -->
                        </tr>
                    </thead>
                    @if (isset($categories) && $categories->isNotEmpty())
                        <tbody>
                            @foreach ($categories as $category)
                                @php
                                    $translation = $category->expertGuideCategoryTranslation->first();
                                @endphp
                                <tr class="nk-tb-item">
                                    <td class="nk-tb-col">
                                        <div class="user-card">
                                            <div class="user-info">
                                                <span class="tb-lead">
                                                    {{ $translation?->name ?? 'N/A' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="nk-tb-col tb-col-mb">
                                        <span class="tb-lead">
                                            {{ $translation?->slug ?? 'N/A' }}
                                        </span>
                                    </td>

                                    <td class="nk-tb-col tb-col-mb">
                                        <span class="tb-lead">
                                            {!! \Illuminate\Support\Str::words($translation?->description ?? 'N/A', 10, '...') !!}
                                        </span>
                                    </td>

                                    <!-- Add Image Column -->
                                    <td class="nk-tb-col">
                                        @if ($category->image)
                                            <img src="{{ asset($category->image) }}"
                                                alt="{{ $translation?->name ?? 'Category Image' }}"
                                                style="width: 50px; height: auto;">
                                        @else
                                            <span>No Image</span>
                                        @endif
                                    </td>

                                    <!-- Add Action Column -->
                                    <td class="nk-tb-col nk-tb-col-tools">
                                        <ul class="nk-tb-actions gx-1">
                                            <li>
                                                <div class="drodown">
                                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                        data-bs-toggle="dropdown">
                                                        <em class="icon ni ni-more-h"></em>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end" style="height:auto;">
                                                        <ul class="link-list-opt no-bdr">
                                                            <li>
                                                                <a
                                                                    href="{{ route('admin-expert-guide-add-category', ['id' => $category->id]) }}">
                                                                    <em class="icon ni ni-edit-fill"></em><span>Edit</span>
                                                                </a>
                                                            </li>
                                                            <li class="removeConfermation"
                                                                data-url="{{ route('admin-expert-guide-delete-category', ['id' => $category->id]) }}">
                                                                <a class="delete"
                                                                    href="{{ route('admin-expert-guide-delete-category', ['id' => $category->id]) }}">
                                                                    <em
                                                                        class="icon ni ni-trash-fill"></em><span>Remove</span>
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
