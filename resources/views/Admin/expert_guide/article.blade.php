@extends('admin_layout.master')
@section('content')

<?php

$lang = getCurrentLocale();

?>
<div class="nk-block nk-block-lg expert-guide-article">
<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title">Article</h3>
            </div>
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li class="nk-block-tools-opt">
                                        <a href="{{ url('admin-dashboard/article/add') ?? '' }}" class=" btn btn-icon btn-primary d-md-none"><em class=""></em></a>
                                        @if(getCurrentLanguageID() === 1)
                                        <a href="{{ route('admin-expert-guide-add-article')}}" class=" btn btn-primary d-none d-md-inline-flex"><em class=""></em><span>Add Article</span></a>
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
                        <th class="nk-tb-col"><span class="sub-text">Article Name</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Category Name</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Description</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Image</span></th>
                        <th class="nk-tb-col tb-tnx-action">
                            <span>Action</span>
                        </th>
                    </tr>
                </thead>
                @if(isset($articles) && $articles->isNotEmpty())
                <tbody>
                    @foreach($articles as $article)
                        @php
                            $translation = optional($article->articleTranslations)->first();
                            $categoryTranslation = optional(optional($article->category)->expertGuideCategoryTranslation)->first();
                        @endphp

                        <tr class="nk-tb-item">
                            <td class="nk-tb-col">
                                <div class="user-card">
                                    <div class="user-info">
                                        <span class="tb-lead">
                                            {{ $translation->title ?? 'No Title' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td class="nk-tb-col">
                                <span class="tb-lead">
                                    {{ $categoryTranslation->name ?? 'No Category' }}
                                </span>
                            </td>

                            <td class="nk-tb-col tb-col-mb">
                                <span class="tb-lead">
                                    {{ strip_tags($translation->description ?? 'No Description') }}
                                </span>
                            </td>

                            <td class="nk-tb-col">
                                @if($article->image)
                                    <img src="{{ asset($article->image) }}" alt="{{ $translation->title ?? 'Article Image' }}" style="width: 50px; height: auto;">
                                @else
                                    <span>No Image</span>
                                @endif
                            </td>

                            <td class="nk-tb-col nk-tb-col-tools">
                                <ul class="nk-tb-actions gx-1">
                                    <li>
                                        <div class="drodown">
                                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                                <em class="icon ni ni-more-h"></em>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end" style="height:auto;">
                                                <ul class="link-list-opt no-bdr">
                                                    <li>
                                                        <a href="{{ route('admin-expert-guide-add-article',['id'=>$article->id]) }}">
                                                            <em class="icon ni ni-edit-fill"></em><span>Edit</span>
                                                        </a>
                                                    </li>

                                                    <li class="removeConfermation"
                                                    data-url="{{ route('admin-expert-guide-delete-article', ['id' => $article->id]) }}">
                                                            <a href="{{ route('admin-expert-guide-delete-article',['id'=>$article->id])  }}">
                                                                <em class="icon ni ni-trash-fill"></em><span>Remove</span>
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
