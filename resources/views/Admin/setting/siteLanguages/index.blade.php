@extends('admin_layout.master')
@section('content')

<div class="nk-block nk-block-lg country-language">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Site Languages</h3>
            </div>
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em
                            class="icon ni ni-more-v"></em></a>
                    <div class="toggle-expand-content" data-content="pageMenu">
                        <ul class="nk-block-tools g-3">
                            <li class="nk-block-tools-opt">
                                @if(getCurrentLanguageID() === 1)
                                    <a href="{{ route('site-languages-add')}}"
                                    class=" btn btn-primary d-none d-md-inline-flex btn-localio"><em
                                        class=""></em><span> Add Country/Region
                                    </span></a>
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
                        <th class="nk-tb-col"><span class="sub-text">Lang Code</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Country</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Base Language</span></th>
                        <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                        <th class="nk-tb-col tb-tnx-action">
                            <span>Action</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($siteLanguages as $siteLanguage)
                        <tr class="nk-tb-item">
                            <td class="nk-tb-col">
                                <div class="user-card">
                                    <div class="user-info">
                                        <span class="tb-lead">{{ $siteLanguage->name ?? '' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="nk-tb-col tb-col-mb">
                                <span class="tb-amount">{{ $siteLanguage->lang_code ?? '' }}</span>
                            </td>
                            <td class="nk-tb-col tb-col-md">
                                <span class="tb-amount">{{ $siteLanguage->country->name ?? '' }}</span>
                            </td>

                            <td class="nk-tb-col tb-col-md">
                                <span class="tb-amount">
                                    {{ $siteLanguage->baseLanguage ? $siteLanguage->baseLanguage->name : 'N/A' }}
                                </span>
                            </td>

                            <td class="nk-tb-col tb-col-md">
                                @if($siteLanguage->status)
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
                                                        <a href="{{ route('site-language-update', $siteLanguage->id) }}">
                                                            <em class="icon ni ni-edit-fill"></em>
                                                            <span>Edit</span>
                                                        </a>
                                                    </li>
                                                    <li data-url="{{ route('site-language-toggle-status', $siteLanguage->id) }}">
                                                        <a href="{{ route('site-language-toggle-status', $siteLanguage->id) }}">
                                                            <em class="icon ni ni-{{ $siteLanguage->status ? 'cross' : 'check' }}-circle-fill"></em>
                                                            <span>{{ $siteLanguage->status ? 'Disable' : 'Enable' }}</span>
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
