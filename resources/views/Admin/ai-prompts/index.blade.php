@extends('admin_layout.master')

@section('title', 'AI Prompts')

@section('content')
<div class="nk-content-inner all-prompts">
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">AI Prompts</h3>
                    <p class="nk-block-des text-soft">Manage your AI prompt templates for automated content generation.</p>
                </div>
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                        <div class="toggle-expand-content" data-content="more-options">
                            <ul class="nk-block-tools g-3">
                                <li class="nk-block-tools-opt">
                                    <a href="{{ route('ai-prompts.create') }}" class="btn btn-primary">
                                        <span>Add Prompt</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered card-stretch">
                <div class="card-inner-group">
                    <div class="card-inner position-relative card-tools-toggle">
                        <div class="card-title-group">
                            <div class="card-tools">
                                <form method="GET" action="{{ route('ai-prompts.index') }}" class="d-flex">
                                    <div class="form-group me-2">
                                        <select name="type" class="form-control form-select" onchange="this.form.submit()">
                                            <option value="">All Types</option>
                                            <option value="business_description" {{ request('type') == 'business_description' ? 'selected' : '' }}>Business Description</option>
                                            <option value="company_info" {{ request('type') == 'company_info' ? 'selected' : '' }}>Company Info</option>
                                            <option value="features" {{ request('type') == 'features' ? 'selected' : '' }}>Features</option>
                                            <option value="full_autofill" {{ request('type') == 'full_autofill' ? 'selected' : '' }}>Full Autofill</option>
                                            <option value="seo" {{ request('type') == 'seo' ? 'selected' : '' }}>SEO</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-inner p-0">
                        <div class="nk-tb-list nk-tb-bol">
                            <div class="nk-tb-item nk-tb-head">
                                <div class="nk-tb-col"><span class="sub-text">Name</span></div>
                                <div class="nk-tb-col tb-col-md"><span class="sub-text">Type</span></div>
                                <div class="nk-tb-col tb-col-lg"><span class="sub-text">Description</span></div>
                                <div class="nk-tb-col tb-col-sm"><span class="sub-text">Status</span></div>
                                {{-- <div class="nk-tb-col tb-col-sm"><span class="sub-text">Tokens</span></div>
                                <div class="nk-tb-col tb-col-sm"><span class="sub-text">Temperature</span></div> --}}
                                <div class="nk-tb-col tb-col-sm"><span class="sub-text">Prompt</span></div>
                                <div class="nk-tb-col tb-col-sm"><span class="sub-text">Actions</span></div>
                            </div>

                            @forelse($prompts as $prompt)
                            <div class="nk-tb-item">
                                <div class="nk-tb-col">
                                    <div class="user-card">
                                        <div class="user-info">
                                            <span class="tb-lead">{{ $prompt->name }}</span>

                                        </div>
                                    </div>
                                </div>
                                <div class="nk-tb-col">
                                    <div class="user-card">
                                        <div class="user-info">
                                            <span class="tb-lead">{{ ucfirst(str_replace('_', ' ', $prompt->type)) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="nk-tb-col tb-col-lg">
                                    <span class="tb-sub">{{ Str::limit($prompt->description, 50) }}</span>
                                </div>
                                <div class="nk-tb-col tb-col-sm">
                                    @if($prompt->is_active)
                                        <span class="badge-success">Active</span>
                                    @else
                                        <span class="badge-secondary">Inactive</span>
                                    @endif
                                </div>

                                <div class="nk-tb-col tb-col-sm">
                                    <span class="tb-sub">{{ $prompt->original_prompt }}</span>
                                </div>
                                <div class="nk-tb-col tb-col-sm">
                                    <div class="dropdown">
                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                            <em class="icon ni ni-more-h"></em>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <ul class="link-list-opt no-bdr">
                                                <li>
                                                    <a href="{{ route('ai-prompts.edit', $prompt) }}">
                                                        <em class="icon ni ni-edit"></em>
                                                        <span>Edit</span>
                                                    </a>
                                                </li>
                                                <li class="divider"></li>
                                                <li>
                                                    <a href="{{ route('ai-prompts.destroy', $prompt) }}"
                                                       onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this prompt?')) { document.getElementById('delete-form-{{ $prompt->id }}').submit(); }">
                                                        <em class="icon ni ni-trash"></em>
                                                        <span>Delete</span>
                                                    </a>
                                                    <form id="delete-form-{{ $prompt->id }}" action="{{ route('ai-prompts.destroy', $prompt) }}" method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="nk-tb-item">
                                <div class="nk-tb-col text-center" colspan="7">
                                    <div class="py-4">
                                        <em class="icon ni ni-inbox" style="font-size: 3rem; opacity: 0.3;"></em>
                                        <p class="text-muted mt-2">No AI prompts found.</p>
                                        <a href="{{ route('ai-prompts.create') }}" class="btn btn-primary btn-sm mt-2">Create First Prompt</a>
                                    </div>
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    @if($prompts->hasPages())
                    <div class="card-inner">
                        <div class="nk-block-between-md g-3">
                            <div class="g">
                                {{ $prompts->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
