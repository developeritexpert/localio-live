@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">
                    <em class="icon ni ni-opt-dot text-primary me-2"></em> Review Moderation Settings
                </h3>
                <p class="text-soft">Configure OpenAI Moderation and GPT-5.6-Luna Batch processing parameters.</p>
            </div>
            <div class="nk-block-head-content d-flex gap-2">
                <a href="{{ route('admin.reviews.batches') }}" class="btn btn-outline-primary btn-sm">
                    <em class="icon ni ni-layers me-1"></em> Review Batches
                </a>
                <a href="{{ route('reviews') }}" class="btn btn-outline-secondary btn-sm">
                    <em class="icon ni ni-arrow-left"></em> All Reviews
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-icon mb-4">
            <em class="icon ni ni-check-circle"></em> {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        <!-- Layer 2 Config Card -->
        <div class="col-md-6">
            <div class="card card-bordered h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 text-dark"><em class="icon ni ni-shield-check text-success me-1"></em> Layer 2 � OpenAI Moderation API</h6>
                </div>
                <div class="card-inner">
                    <p class="text-soft fs-13px mb-3">
                        Real-time harmful safety filter. Per OpenAI policy, the moderation model is completely free and evaluates harassment, hate, self-harm, sexual, and violent content immediately on review submission.
                    </p>
                    <div class="mb-3">
                        <label class="form-label">Active Model</label>
                        <input type="text" class="form-control" value="{{ $settings['moderation_model'] }}" readonly disabled>
                        <span class="form-note text-muted">Fixed implementation setting (omni-moderation-latest).</span>
                    </div>
                    <div class="alert alert-info p-2 fs-12px mb-0">
                        <em class="icon ni ni-info me-1"></em> Reviews flagged here skip Layer 3 GPT analysis to conserve cost and escalate directly to human moderators.
                    </div>
                </div>
            </div>
        </div>

        <!-- Layer 3 Config Card -->
        <div class="col-md-6">
            <div class="card card-bordered h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 text-dark"><em class="icon ni ni-cpu text-primary me-1"></em> Layer 3 � Localio Review Integrity & Locale AI</h6>
                </div>
                <div class="card-inner">
                    <form action="{{ route('admin.reviews.settings.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Production Model</label>
                            <input type="text" class="form-control" value="{{ $settings['review_ai_model'] }}" readonly disabled>
                            <span class="form-note text-muted">GPT-5.6 Luna via OpenAI Batch API (/v1/responses).</span>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-8">
                                <label class="form-label">Stored Prompt ID <span class="text-danger">*</span></label>
                                <input type="text" name="review_prompt_id" class="form-control" value="{{ old('review_prompt_id', $settings['review_prompt_id']) }}" required placeholder="e.g. pmpt_rev_moderation_v1">
                            </div>
                            <div class="col-4">
                                <label class="form-label">Version <span class="text-danger">*</span></label>
                                <input type="text" name="review_prompt_version" class="form-control" value="{{ old('review_prompt_version', $settings['review_prompt_version']) }}" required>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">Reasoning Effort</label>
                                <select name="review_reasoning_effort" class="form-select">
                                    <option value="low" {{ $settings['review_reasoning_effort'] === 'low' ? 'selected' : '' }}>Low (Recommended)</option>
                                    <option value="medium" {{ $settings['review_reasoning_effort'] === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ $settings['review_reasoning_effort'] === 'high' ? 'selected' : '' }}>High</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Batch Interval (Hours)</label>
                                <input type="number" name="review_batch_hours" class="form-control" min="1" max="48" value="{{ old('review_batch_hours', $settings['review_batch_hours']) }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm">Save Moderation Settings</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Supported Locales Card -->
        <div class="col-12">
            <div class="card card-bordered">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 text-dark"><em class="icon ni ni-globe text-info me-1"></em> Supported Localio Locales</h6>
                </div>
                <div class="card-inner">
                    <p class="text-soft fs-13px mb-2">
                        These exact BCP-47 locale codes are supplied to GPT for review-locale verification. The model must match or reassign reviews exclusively to one of these supported codes.
                    </p>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($settings['supported_locales'] as $locale)
                            <span class="badge bg-light text-dark border"><code>{{ $locale }}</code></span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
