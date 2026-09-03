@extends('admin_layout.master')
@section('title', 'Edit AI Prompt')

@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Edit AI Prompt</h3>
                    <p class="nk-block-des text-soft">Update the AI prompt template: <strong>{{ $aiPrompt->name }}</strong></p>
                </div>
            </div>
        </div>

        <div class="nk-block">
            <div class="card card-bordered">
                <div class="card-inner">
                    <form action="{{ route('ai-prompts.update', $aiPrompt) }}" method="POST" class="form-validate">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label" for="name">Prompt Name <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $aiPrompt->name) }}" required>
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label" for="type">Prompt Type</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="type" name="type" value="{{ old('type', $aiPrompt->type) }}" placeholder="e.g. business_description, seo, features">
                                        <div class="form-note"><small>Internal label to categorize this prompt</small></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="description">Description</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control" id="description" name="description" rows="2" placeholder="Brief description of what this prompt does">{{ old('description', $aiPrompt->description) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="original_prompt">Prompt Template (local reference only)</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control" id="original_prompt" name="original_prompt" rows="6" placeholder="Optional: store a local copy of the prompt for reference. The actual prompt lives in ChatGPT/Claude.">{{ old('original_prompt', $aiPrompt->original_prompt) }}</textarea>
                                        <div class="form-note"><small class="text-muted">This is for reference only. The real prompt is saved in ChatGPT/Claude and referenced by ID below.</small></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ old('is_active', $aiPrompt->is_active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">Active Status</label>
                                    </div>
                                </div>
                            </div>

                            {{-- =====================================================
                                 CONTENT GENERATION SECTION
                                 Only needed if this prompt is used for batch generation
                            ====================================================== --}}
                            <div class="col-12 mt-2">
                                <div class="p-3" style="background:#f5f6fa;border-radius:8px;border:1px solid #e5e9f2">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <em class="icon ni ni-cpu text-primary fs-5"></em>
                                        <div>
                                            <h6 class="mb-0">Content Generation (Batch API)</h6>
                                            <small class="text-soft">Configure this prompt for AI batch content generation. Leave empty if not used for batch generation.</small>
                                        </div>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label" for="content_field">
                                                    Content Field
                                                    <span class="badge badge-dim bg-light text-dark ms-1">Optional</span>
                                                </label>
                                                <select class="form-control form-select" id="content_field" name="content_field">
                                                    <option value="">-- Not a content gen prompt --</option>
                                                    @foreach(\App\Models\AiPrompt::FIELD_LABELS as $val => $label)
                                                        <option value="{{ $val }}" {{ old('content_field', old('content_field', $aiPrompt->content_field ?? '')) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="form-note"><small>Which content field this prompt generates for a business</small></div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label" for="openai_prompt_id">
                                                    OpenAI Prompt ID
                                                    <span class="badge badge-dim bg-success text-success ms-1">Saved in ChatGPT</span>
                                                </label>
                                                <input type="text" class="form-control font-monospace" id="openai_prompt_id" name="openai_prompt_id"
                                                    value="{{ old('openai_prompt_id', $aiPrompt->openai_prompt_id ?? '') }}"
                                                    placeholder="pmpt_xxxxxxxxxxxxx">
                                                <div class="form-note"><small>The prompt ID from OpenAI Prompt Management dashboard</small></div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label" for="anthropic_prompt_id">
                                                    Anthropic Prompt ID
                                                    <span class="badge badge-dim bg-warning text-warning ms-1">Saved in Claude</span>
                                                </label>
                                                <input type="text" class="form-control font-monospace" id="anthropic_prompt_id" name="anthropic_prompt_id"
                                                    value="{{ old('anthropic_prompt_id', $aiPrompt->anthropic_prompt_id ?? '') }}"
                                                    placeholder="prmpt_xxxxxxxxxxxxx">
                                                <div class="form-note"><small class="text-soft">Note: Claude/Anthropic does not offer a prompt-by-ID API. For Anthropic batches, the full prompt text above will always be dynamically rendered.</small></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-lg btn-primary">
                                <em class="icon ni ni-save"></em> <span>Update Prompt</span>
                            </button>
                            <a href="{{ route('ai-prompts.index') }}" class="btn btn-lg btn-outline-light ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
