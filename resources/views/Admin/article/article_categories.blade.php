@extends('admin_layout.master')
@section('content')

<?php
    $lang = getCurrentLocale();
?>
<div class="nk-block nk-block-lg">
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Article Category</h3>
            </div>
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                        <div class="toggle-expand-content" data-content="pageMenu">
                            <ul class="nk-block-tools g-3">
                                <li class="nk-block-tools-opt">
                                    <a href="{{ url('admin-dashboard/article/category/add') ?? '' }}" class=" btn btn-icon btn-primary d-md-none"><em class=""></em></a>
                                    <a href="{{ url('admin-dashboard/article/category/add') ?? '' }}" class=" btn btn-primary d-none d-md-inline-flex"><em class=""></em><span>Add Article Category</span></a>
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
                @if(isset($articleCategory) && $articleCategory->isNotEmpty())
                    <tbody>
                        @foreach($articleCategory as $category)
                            <tr class="nk-tb-item">
                                <td class="nk-tb-col">
                                    <div class="user-card">
                                        <div class="user-info">
                                            <span class="tb-lead">
                                            {{ $category->translations->isNotEmpty() ? $category->translations->first()->name : $category->name }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="nk-tb-col tb-col-mb">
                                    <span class="tb-lead">{{ $category->translations->isNotEmpty() ? $category->translations->first()->slug : $category->slug }}</span>
                                </td>
                                <td class="nk-tb-col tb-col-mb">
                                    <span class="tb-lead">
                                    <!-- {{ $category->translations->isNotEmpty() ? $category->translations->first()->description : $category->description }} -->
                                    {!! \Illuminate\Support\Str::words( $category->translations->isNotEmpty() ? $category->translations->first()->description : $category->description ?? 'No description available', 10, '...') !!}
                                    </span>
                                </td>
                                <!-- Add Image Column -->
                                <td class="nk-tb-col">
                                    @if($category->image)
                                        <img src="{{ asset('ArticleCategoryImages/' . $category->image) }}" alt="{{ $category->name }}" style="width: 50px; height: auto;">
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
                                                    data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <ul class="link-list-opt no-bdr">
                                                        <li><a
                                                                href="{{ url('admin-dashboard/edit-article-category') ?? '' }}/{{ $category->id ?? ''}}/{{ $category->language_id ?? ''}}"><em class="icon ni ni-edit-fill"></em><span>Edit</span></a></li>

                                                        @if($lang == 'en')
                                                            <!-- <li class="removeConfermation" data-url="{{ url('admin-dashboard/remove-article-category') ?? '' }}/{{ $categoryTranslation->id ?? ''}}"> -->
                                                                <a class="delete" href="{{ url('admin-dashboard/remove-article-category') ?? '' }}/{{ $category->id ?? ''}}"><em class="icon ni ni-trash-fill"></em><span>Remove</span></a>
                                                            <!-- </li> -->
                                                        @endif
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
