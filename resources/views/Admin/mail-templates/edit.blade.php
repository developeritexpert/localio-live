@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Edit Mail Template: <code>{{ $mailTemplate->key }}</code></h4>
            </div>
            <div class="nk-block-head-content">
                <a href="{{ route('mail-templates.show', $mailTemplate) }}" class="btn" style="
                background-color: #F9633B;
                color: white;">
                 View
                </a>
            </div>
        </div>
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('mail-templates.update', $mailTemplate) }}" method="POST" class="form-validate">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="lang_id" value="{{ $langId }}">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <div class="row g-gs">
                        <!-- Language Selector -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Edit Language Version</label>
                                <div class="form-control-wrap">
                                    <select class="form-select" id="language-selector">
                                        @foreach ($languages as $language)
                                            <option value="{{ $language->id }}"
                                                {{ $langId == $language->id ? 'selected' : '' }}>
                                                {{ $language->name }}
                                                @if ($language->id == 1)
                                                    (Default)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            @if ($langId != 1)
                                <div class="alert alert-info mt-3">
                                    <em class="icon ni ni-info"></em>
                                    You are editing the translation for
                                    <strong>{{ $languages->find($langId)->name ?? 'Selected Language' }}</strong>
                                </div>
                            @endif
                        </div>

                        @if ($langId == 1)
                            <!-- Template Key (Only for default language) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="key">Template Key</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="key" name="key"
                                            value="{{ old('key', $mailTemplate->key) }}"
                                            placeholder="e.g., forget_password, welcome_email">
                                    </div>
                                    <div class="form-note">Unique identifier for this template (use snake_case)</div>
                                    @error('key')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status (Only for default language) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <div class="form-control-wrap">
                                        <select class="form-select" name="status">
                                            <option value="active"
                                                {{ old('status', $mailTemplate->status) == 'active' ? 'selected' : '' }}>
                                                Active</option>
                                            <option value="inactive"
                                                {{ old('status', $mailTemplate->status) == 'inactive' ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>
                                    </div>
                                    @error('status')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="key" value="{{ $mailTemplate->key }}">
                            <input type="hidden" name="status" value="{{ $mailTemplate->status }}">
                        @endif

                        <!-- Subject -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="subject">Subject</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="subject" name="subject"
                                        value="{{ old('subject', $translation['subject'] ?? '') }}"
                                        placeholder="Email subject line">
                                </div>
                                <div class="form-note">You can use variables like: &#123;&#123; $variable_name &#125;&#125;
                                </div>
                                @error('subject')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email Body Content -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="body">Email Content</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control description" id="body" name="body" rows="15">
                                        {{ old('body', $translation['body'] ?? '') }}</textarea>
                                </div>
                                <div class="form-note">
                                    Enter only the dynamic content. The HTML structure will be handled by the view template.
                                    <br>You can use variables like: &#123;&#123; $variable_name &#125;&#125;
                                </div>
                                @error('body')
                                    <div class="error text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @if ($langId == 1)
                            <!-- Variables (Only for default language) -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Available Variables</label>
                                    <div id="variables-container">
                                        @if ($mailTemplate->variables && count($mailTemplate->variables) > 0)
                                            @foreach ($mailTemplate->variables as $variable)
                                                <div class="input-group mb-2 variable-input">
                                                    <div class="form-control-wrap flex-grow-1">
                                                        <input type="text" class="form-control" name="variables[]"
                                                            value="{{ $variable }}"
                                                            placeholder="Variable name (without $)">
                                                    </div>
                                                    <div class="input-group-append">
                                                        <button type="button"
                                                            class="btn btn-outline-danger remove-variable">
                                                            <em class="icon ni ni-cross"></em>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="input-group mb-2 variable-input">
                                                <div class="form-control-wrap flex-grow-1">
                                                    <input type="text" class="form-control" name="variables[]"
                                                        placeholder="Variable name (without $)">
                                                </div>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-danger remove-variable">
                                                        <em class="icon ni ni-cross"></em>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-variable">
                                        <em class="icon ni ni-plus"></em> Add Variable
                                    </button>
                                    <div class="form-note">
                                        Define variables that can be used in subject and body (e.g., name, email, otp)
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Template Preview -->
                        <div class="col-md-12">
                            <div class="card card-bordered">
                                <div class="card-inner-group">
                                    <div class="card-inner">
                                        <div class="card-title-group">
                                            <div class="card-title">
                                                <h6 class="title">Template Preview</h6>
                                            </div>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                    id="preview-btn">
                                                    <em class="icon ni ni-eye"></em> Preview
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-inner">
                                        <div id="preview-content">
                                            <p class="text-muted">Click "Preview" to see how your template will look.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="col-md-12 mt-4">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" style="background:#F9633B">
                                    <em class="icon ni ni-save"></em> Update Template
                                </button>
                                <a href="{{ route('mail-templates.index') }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
   document.addEventListener('DOMContentLoaded', function() {
    // Get language ID from server
    const langId = {{ $langId }};

    // Language selector change
    const languageSelector = document.getElementById('language-selector');
    if (languageSelector) {
        languageSelector.addEventListener('change', function() {
            const selectedLangId = this.value;
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('lang_id', selectedLangId);
            window.location.href = currentUrl.toString();
        });
    }

    // Add variable functionality (only for default language)
    if (langId == 1) {
        const addVariableBtn = document.getElementById('add-variable');
        if (addVariableBtn) {
            addVariableBtn.addEventListener('click', function() {
                const container = document.getElementById('variables-container');
                const newInput = document.createElement('div');
                newInput.className = 'input-group mb-2 variable-input';
                newInput.innerHTML = `
                    <div class="form-control-wrap flex-grow-1">
                        <input type="text" class="form-control" name="variables[]" placeholder="Variable name (without $)">
                    </div>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-danger remove-variable">
                            <em class="icon ni ni-cross"></em>
                        </button>
                    </div>
                `;
                container.appendChild(newInput);
            });
        }

        // Remove variable functionality
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-variable')) {
                const variableInputs = document.querySelectorAll('.variable-input');
                if (variableInputs.length > 1) {
                    e.target.closest('.variable-input').remove();
                }
            }
        });
    }

    // Preview functionality
    const previewBtn = document.getElementById('preview-btn');
    if (previewBtn) {
        previewBtn.addEventListener('click', function() {
            const subjectInput = document.getElementById('subject');
            const bodyInput = document.getElementById('body');

            const subject = subjectInput ? subjectInput.value : '';
            const body = bodyInput ? bodyInput.value : '';

            let variables = [];

            if (langId == 1) {
                // For default language, get variables from input fields
                const variableInputs = document.querySelectorAll('input[name="variables[]"]');
                variables = Array.from(variableInputs)
                    .map(input => input.value.trim())
                    .filter(value => value !== '');
            } else {
                // For other languages, get variables from server
                variables = {!! json_encode($mailTemplate->variables ?? []) !!};
            }

            // Create sample data for preview
            const sampleData = {};
            variables.forEach(variable => {
                sampleData[variable] = `[${variable}]`;
            });

            // Simple variable replacement for preview
            let previewSubject = subject;
            let previewBody = body;

            Object.keys(sampleData).forEach(key => {
                const regex = new RegExp("\\{\\{\\s*\\$" + key + "\\s*\\}\\}", "g");
                previewSubject = previewSubject.replace(regex, sampleData[key]);
                previewBody = previewBody.replace(regex, sampleData[key]);
            });

            const previewContent = document.getElementById('preview-content');
            if (previewContent) {
                previewContent.innerHTML = `
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label"><strong>Subject:</strong></label>
                                <div class="form-control-wrap">
                                    <div class="form-control-plaintext bg-lighter border rounded p-2">
                                        ${previewSubject || 'No subject'}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label"><strong>Body:</strong></label>
                                <div class="form-control-wrap">
                                    <div class="form-control-plaintext bg-lighter border rounded p-3" style="min-height: 150px; white-space: pre-wrap;">
                                        ${previewBody || 'No content'}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        });
    }
});
    </script>
@endpush
