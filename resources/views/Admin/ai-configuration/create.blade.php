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

            {{-- ===== JSON Format Documentation ===== --}}
            <div class="col-12">
                <div class="card card-bordered" style="border-left: 4px solid #6576ff;">
                    <div class="card-header d-flex align-items-center ">
                        <em class="icon ni ni-info-fill text-primary fs-4"></em>
                        <div style="margin-left: 25px;">
                            <h5 class="mb-0">Prompt Format Documentation</h5>
                            <small class="text-soft">How we communicate with your saved prompts — copy this when setting up prompts in ChatGPT/Claude</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">

                            <div class="col-md-6">
                                <h6 class="mb-2"><em class="icon ni ni-arrow-right text-primary"></em> Input Variables We Send</h6>
                                <p class="text-soft small mb-2">These variables are passed to your saved prompt. Reference them in your prompt template:</p>
                                <pre class="p-3 rounded" style="background:#1e2134;color:#a9b7c6;font-size:0.8rem;border-radius:8px">@{{
  "business_name":    "Bluehost",
  "category_name":    "Web Hosting",
  "subcategory_name": "Shared Hosting"
}}</pre>
                                <p class="text-muted small mt-2">In your ChatGPT/Claude prompt, reference these as <code>@{{business_name}}</code>, <code>@{{category_name}}</code>, etc.</p>
                            </div>

                            <div class="col-md-6">
                                <h6 class="mb-2"><em class="icon ni ni-arrow-left text-success"></em> Expected JSON Output Per Field</h6>
                                <p class="text-soft small mb-2">Your prompt must return <strong>valid JSON only</strong> — no markdown, no explanation text:</p>

                                <div class="accordion" id="formatAccordion">
                                    @php
                                        $formats = [
                                            'content'   => ['Content / Overview', '{ "content": "Full overview text of the business..." }'],
                                            'faqs'      => ['FAQs', '{ "faqs": [{"question": "What is it?", "answer": "It is..."}] }'],
                                            'features'  => ['Features', '{ "features": [{"title": "Feature Name", "description": "Short desc..."}] }'],
                                            'pros_cons' => ['Pros & Cons', '{ "pros": ["Fast", "Easy to use"], "cons": ["Expensive"] }'],
                                            'metatags'  => ['Meta Tags (SEO)', '{ "meta_title": "Bluehost Review (max 60 chars)", "meta_description": "Full description (max 155 chars)" }'],
                                        ];
                                    @endphp
                                    @foreach($formats as $key => [$label, $example])
                                    <div class="mb-1">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-light text-dark border">{{ $key }}</span>
                                            <small class="text-soft">{{ $label }}</small>
                                        </div>
                                        <pre class="p-2 rounded mb-2" style="background:#f5f6fa;font-size:0.75rem;color:#364a63;border:1px solid #e5e9f2">{{ $example }}</pre>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    <em class="icon ni ni-bulb-fill me-1"></em>
                                    <strong>How to link a prompt:</strong>
                                    Go to <a href="{{ route('ai-prompts.index') }}" class="alert-link">AI Prompts</a>,
                                    create or edit a prompt, set the <strong>Content Field</strong> (e.g. "FAQs"),
                                    and paste the <strong>OpenAI Prompt ID</strong> (e.g. <code>pmpt_xxxx</code>) from your ChatGPT dashboard.
                                    The system will then send only that ID + the input variables — no prompt text.
                                </div>
                            </div>

                            <div class="col-12">
                                <h6 class="mb-2"><em class="icon ni ni-link text-warning"></em> Configured Prompts per Field</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Content Field</th>
                                                <th>Prompt Name</th>
                                                <th>OpenAI Prompt ID</th>
                                                <th>Anthropic Prompt ID</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(\App\Models\AiPrompt::FIELD_LABELS as $field => $label)
                                                @php $p = \App\Models\AiPrompt::forContentField($field); @endphp
                                                <tr>
                                                    <td><code>{{ $field }}</code><br><small class="text-soft">{{ $label }}</small></td>
                                                    <td>{{ $p?->name ?? '<span class="text-muted">Not configured</span>' }}</td>
                                                    <td>
                                                        @if($p?->openai_prompt_id)
                                                            <code class="text-success">{{ $p->openai_prompt_id }}</code>
                                                        @else
                                                            <span class="badge badge-dim bg-danger text-danger">Not set</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($p?->anthropic_prompt_id)
                                                            <code class="text-success">{{ $p->anthropic_prompt_id }}</code>
                                                        @else
                                                            <span class="badge badge-dim bg-light text-muted">Not set</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($p)
                                                            <a href="{{ route('ai-prompts.edit', $p) }}" class="btn btn-xs btn-outline-primary">Edit</a>
                                                        @else
                                                            <a href="{{ route('ai-prompts.create') }}" class="btn btn-xs btn-outline-secondary">Create</a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
.ai-provider-logo { width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.openai-logo { background:#10a37f20;color:#10a37f; }
.anthropic-logo { background:#d4723a20;color:#d4723a; }
</style>
@endsection
