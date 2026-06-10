@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg email-template">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Mail Templates</h3>
                </div>
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                            <em class="icon ni ni-more-v"></em>
                        </a>
                        <div class="toggle-expand-content" data-content="pageMenu">
                            <ul class="nk-block-tools g-3">
                                <li class="nk-block-tools-opt">
                                    <a href="{{ route('mail-templates.create') }}" class="btn btn-icon btn-primary d-md-none">
                                        <em class="icon ni ni-plus"></em>
                                    </a>
                                    <a href="{{ route('mail-templates.create') }}" class="btn btn-primary d-none d-md-inline-flex btn-localio">
                                        <span>Add Template</span>
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
                <table class="datatable-init nowrap nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col"><span class="sub-text">Template Key</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Subject</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Variables</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Translations</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Created</span></th>
                            <th class="nk-tb-col tb-tnx-action"><span>Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($templates->isNotEmpty())
                            @foreach($templates as $template)
                                <tr class="nk-tb-item">
                                    {{-- Template Key --}}
                                    <td class="nk-tb-col">
                                        <div class="user-info">
                                            <code class="tb-lead">{{ $template->key }}</code>
                                        </div>
                                    </td>

                                    {{-- Subject --}}
                                    <td class="nk-tb-col">
                                        <span class="tb-sub">{{ Str::limit($template->subject, 50) }}</span>
                                    </td>

                                    {{-- Variables --}}
                                    <td class="nk-tb-col">
                                        @if($template->variables && count($template->variables) > 0)
                                            @foreach($template->variables as $variable)
                                                <span class="badge badge-sm badge-dim bg-outline-info">{{ $variable }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="nk-tb-col">
                                        <span class="{{ $template->status === 'active' ? 'text-success' : 'text-danger' }}">
                                            <em class="icon ni ni-{{ $template->status === 'active' ? 'check-circle-fill' : 'cross-circle-fill' }}"></em>
                                            {{ ucfirst($template->status) }}
                                        </span>
                                    </td>

                                    {{-- Translations --}}
                                    <td class="nk-tb-col">
                                        <span class="badge badge-sm badge-dim bg-outline-secondary">
                                            {{ $template->translations->count() }} translations
                                        </span>
                                    </td>

                                    {{-- Created At --}}
                                    <td class="nk-tb-col">
                                        <span class="tb-sub">{{ $template->created_at->format('Y-m-d H:i') }}</span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="nk-tb-col nk-tb-col-tools">
                                        <ul class="nk-tb-actions gx-1">
                                            <li>
                                                <div class="drodown">
                                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                       data-bs-toggle="dropdown">
                                                        <em class="icon ni ni-more-h"></em>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end" style="height: auto">
                                                        <ul class="link-list-opt no-bdr">
                                                            <li>
                                                                <a href="{{ route('mail-templates.show', $template->id) }}">
                                                                    <em class="icon ni ni-eye-fill"></em>
                                                                    <span>View</span>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="{{ route('mail-templates.edit', $template->id) }}">
                                                                    <em class="icon ni ni-edit-fill"></em>
                                                                    <span>Edit</span>
                                                                </a>
                                                            </li>
                                                            <li class="removeConfermation"
                                                                data-url="{{ route('mail-templates.destroy', $template->id) }}">
                                                                <a class="delete" href="{{ route('mail-templates.destroy', $template->id) }}">
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
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-center">
                                        <p class="text-muted">No mail templates found.</p>
                                        <a href="{{ route('mail-templates.create') }}" class="btn btn-primary">
                                            Create Your First Template
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($templates->hasPages())
            <div class="card">
                <div class="card-inner">
                    <div class="nk-block-between-md g-3">
                        <div class="g">
                            {{ $templates->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
