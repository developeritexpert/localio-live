@extends('admin_layout.master')

@section('title', 'AI Configuration')

@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">AI Configuration</h3>
                    <p class="nk-block-des text-soft">Configure your AI provider credentials for batch content generation.</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <em class="icon ni ni-check-circle me-1"></em> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            {{-- OpenAI Section --}}
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-header d-flex align-items-center">
                        <div class="ai-provider-logo openai-logo">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M22.282 9.821a5.985 5.985 0 0 0-.516-4.91 6.046 6.046 0 0 0-6.51-2.9A6.065 6.065 0 0 0 4.981 4.18a5.985 5.985 0 0 0-3.998 2.9 6.046 6.046 0 0 0 .743 7.097 5.98 5.98 0 0 0 .51 4.911 6.051 6.051 0 0 0 6.515 2.9A5.985 5.985 0 0 0 13.26 24a6.056 6.056 0 0 0 5.772-4.206 5.99 5.99 0 0 0 3.997-2.9 6.056 6.056 0 0 0-.747-7.073zm-9.022 12.61a4.476 4.476 0 0 1-2.876-1.04l.141-.081 4.779-2.758a.795.795 0 0 0 .392-.681v-6.737l2.02 1.168a.071.071 0 0 1 .038.052v5.583a4.504 4.504 0 0 1-4.494 4.494zM3.6 18.304a4.47 4.47 0 0 1-.535-3.014l.142.085 4.783 2.759a.771.771 0 0 0 .78 0l5.843-3.369v2.332a.08.08 0 0 1-.032.065L9.74 19.95a4.5 4.5 0 0 1-6.14-1.646zM2.34 7.896a4.485 4.485 0 0 1 2.366-1.973V11.6a.766.766 0 0 0 .388.676l5.815 3.355-2.02 1.168a.076.076 0 0 1-.071 0l-4.83-2.786A4.504 4.504 0 0 1 2.34 7.872zm16.597 3.855l-5.833-3.387L15.119 7.2a.076.076 0 0 1 .071 0l4.83 2.791a4.494 4.494 0 0 1-.676 8.105v-5.678a.79.79 0 0 0-.407-.667zm2.01-3.023l-.141-.085-4.774-2.782a.776.776 0 0 0-.785 0L9.409 9.23V6.897a.066.066 0 0 1 .028-.061l4.83-2.787a4.5 4.5 0 0 1 6.68 4.66zm-12.64 4.135l-2.02-1.164a.08.08 0 0 1-.038-.057V6.075a4.5 4.5 0 0 1 7.375-3.453l-.142.08-4.778 2.758a.795.795 0 0 0-.393.681zm1.097-2.365l2.602-1.5 2.607 1.5v2.999l-2.597 1.5-2.607-1.5z"/>
                            </svg>
                        </div>
                        <div  style="margin-left: 25px;">
                            <h5 class="mb-0">OpenAI (ChatGPT)</h5>
                            <small class="text-soft">Batch content generation via OpenAI Batch API</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('ai-config.save-openai') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                @foreach($openai as $item)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="{{ $item->key }}">{{ $item->name }}</label>
                                            @if($item->field_type === 'password')
                                                <div class="form-control-wrap">
                                                    <a href="#" class="form-icon form-icon-right passcode-switch lg" data-target="{{ $item->key }}">
                                                        <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                        <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                                    </a>
                                                    <input type="password" class="form-control form-control-lg" id="{{ $item->key }}" name="{{ $item->key }}" value="{{ $item->value }}" placeholder="sk-...">
                                                </div>
                                            @else
                                                <input type="text" class="form-control" id="{{ $item->key }}" name="{{ $item->key }}" value="{{ $item->value }}">
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary"><em class="icon ni ni-save me-1"></em> Save OpenAI Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Anthropic Section --}}
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-header d-flex align-items-center">
                        <div class="ai-provider-logo anthropic-logo">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M13.827 3.52h3.603L24 20h-3.603l-6.57-16.48zm-7.258 0H10.2L16.774 20h-3.64L6.57 3.52z"/>
                            </svg>
                        </div>
                        <div style="margin-left: 25px;">
                            <h5 class="mb-0">Anthropic (Claude)</h5>
                            <small class="text-soft">Batch content generation via Anthropic Message Batches API</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('ai-config.save-anthropic') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                @foreach($anthropic as $item)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="{{ $item->key }}">{{ $item->name }}</label>
                                            @if($item->field_type === 'password')
                                                <div class="form-control-wrap">
                                                    <a href="#" class="form-icon form-icon-right passcode-switch lg" data-target="{{ $item->key }}">
                                                        <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                        <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                                    </a>
                                                    <input type="password" class="form-control form-control-lg" id="{{ $item->key }}" name="{{ $item->key }}" value="{{ $item->value }}" placeholder="sk-ant-...">
                                                </div>
                                            @else
                                                <input type="text" class="form-control" id="{{ $item->key }}" name="{{ $item->key }}" value="{{ $item->value }}">
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary"><em class="icon ni ni-save me-1"></em> Save Anthropic Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Legacy Gemini Section --}}
            @if($legacy->count() > 0)
            <div class="col-12">
                <div class="card card-bordered" style="border-color:#f4bd0e !important">
                    <div class="card-header d-flex align-items-center">
                        <em class="icon ni ni-google fs-4 text-warning"></em>
                        <div style="margin-left: 25px;">
                            <h5 class="mb-0">Google Vertex AI <span class="badge badge-dim bg-warning ms-1">Legacy</span></h5>
                            <small class="text-soft">Original Gemini/Vertex AI settings</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin-setting-config-update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="type" value="ai">
                            <div class="row g-3">
                                @foreach($legacy as $item)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="{{ $item->key }}">{{ $item->name }}</label>
                                            <input type="{{ $item->field_type }}" class="form-control" id="{{ $item->key }}" name="{{ $item->key }}" value="{{ $item->value }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-warning"><em class="icon ni ni-save me-1"></em> Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.ai-provider-logo { width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.openai-logo { background:#10a37f20;color:#10a37f; }
.anthropic-logo { background:#d4723a20;color:#d4723a; }
</style>
@endsection
