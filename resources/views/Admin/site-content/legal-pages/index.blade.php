@extends('admin_layout.master')
@section('content')

    <div class="nk-block nk-block-lg pages-legal-documents">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Legal Documents</h3>
                    <div class="nk-block-des text-soft">
                        <p>Manage all legal document texts in one place.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <table class="datatable-init nowrap nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col"><span class="sub-text">S.No</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Document Name</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Slug / Key</span></th>
                            <th class="nk-tb-col tb-tnx-action">
                                <span>Action</span>
                            </th>
                        </tr>
                    </thead>
                    @if (isset($documents))
                        <tbody>
                            @foreach ($documents as $doc)
                                <tr class="nk-tb-item">
                                    <td class="nk-tb-col">
                                        <span class="tb-lead">{{ $loop->iteration }}</span>
                                    </td>
                                    <td class="nk-tb-col">
                                        <span class="tb-lead">{{ $doc->title }}</span>
                                    </td>
                                    <td class="nk-tb-col">
                                        <span class="tb-sub">{{ $doc->key }}</span>
                                    </td>
                                    <td class="nk-tb-col nk-tb-col-tools" style="padding-right: 10.5rem;">
                                        <a href="{{ route('admin.legal_documents.edit', ['slug' => $doc->key]) }}"
                                           class="btn btn-sm btn-primary">
                                           <em class="icon ni ni-edit-fill"></em> <span>Edit Document</span>
                                        </a>
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
