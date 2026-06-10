@extends('admin_layout.master')
@section('title', 'Create AI Prompt')

@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Create AI Prompt</h3>
                    <p class="nk-block-des text-soft">Create a new AI prompt template for automated content generation.</p>
                </div>
            </div>
        </div>

        <div class="nk-block">
            <div class="card card-bordered">
                <div class="card-inner">
                    <form action="{{ route('ai-prompts.store') }}" method="POST" class="form-validate">
                        @csrf

                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label" for="name">Prompt Name <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label" for="type">Prompt Type <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <select class="form-control form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                            <option value="">Select Type</option>
                                            <option value="business" {{ old('type') == 'business' ? 'selected' : '' }}>Business</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="description">Description</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Brief description of what this prompt does">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="original_prompt">Prompt Template <span class="text-danger">*</span></label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control @error('original_prompt') is-invalid @enderror" id="original_prompt" name="original_prompt" rows="8" placeholder="Enter your prompt template here. Use variables like {business_name}, {category}, etc." required>{{ old('original_prompt') }}</textarea>
                                        @error('original_prompt')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-note">
                                            <small class="text-muted">Use curly braces for variables, e.g., {business_name}, {category}, {description}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">Active Status</label>
                                    </div>
                                    <small class="text-muted">Enable this prompt for use in the system</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-lg btn-primary">
                                <em class="icon ni ni-save"></em>
                                <span>Create Prompt</span>
                            </button>
                            <a href="{{ route('ai-prompts.index') }}" class="btn btn-lg btn-outline-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')

@endpush
@endsection
