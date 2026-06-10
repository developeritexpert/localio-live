@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Create Mail Template</h4>
            </div>
        </div>

        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('mail-templates.store') }}" class="form-validate" method="POST">
                    @csrf
                    <div class="row g-gs">
                        <!-- Template Key -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="key">Template Key</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="key" name="key"
                                           value="{{ old('key') }}" placeholder="e.g., forget_password, welcome_email">
                                </div>
                                <div class="form-note">Unique identifier for this template (use snake_case)</div>
                                @error('key')
                                <div class="error text-danger">{{ $message }}</div>
                            @enderror
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <div class="form-control-wrap">
                                    <select class="form-select" name="status">
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                @error('status')
                                <div class="error text-danger">{{ $message }}</div>
                            @enderror
                            </div>
                        </div>

                        <!-- Subject -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="subject">Subject</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="subject" name="subject"
                                           value="{{ old('subject') }}" placeholder="Email subject line">
                                </div>
                                <div class="form-note">You can use variables like:  &#123;&#123; $variable_name &#125;&#125;</div>
                                @error('subject')
                                <div class="error text-danger">{{ $message }}</div>
                            @enderror
                            </div>
                        </div>

                        <!-- Email Body -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="body">Email Body</label>
                                <div class="form-control-wrap">
                                    <textarea class="form-control description" id="body" name="body" rows="15">{{ old('body') }}</textarea>
                                </div>
                                <div class="form-note">
                                    <br>You can use variables like:  &#123;&#123; $variable_name &#125;&#125;
                                </div>
                                @error('body')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                            </div>
                        </div>
                        <!-- Variables -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Available Variables</label>
                                <div id="variables-container">
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
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="add-variable">
                                    <em class="icon ni ni-plus"></em> Add Variable
                                </button>
                                <div class="form-note">
                                    Define variables that can be used in subject and body (e.g., name, email, otp)
                                </div>
                            </div>
                        </div>

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
                                                <button type="button" class="btn btn-outline-primary btn-sm" id="preview-btn">
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

                        <!-- Submit -->
                        <div class="col-md-12 mt-4">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" style="background:#F9633B">
                                    <em class="icon ni ni-save"></em> Create Template
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
@verbatim
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add variable functionality
    document.getElementById('add-variable').addEventListener('click', function() {
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
    // Remove variable functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-variable')) {
            e.target.closest('.variable-input').remove();
        }
    });
    // Preview functionality
    document.getElementById('preview-btn').addEventListener('click', function() {
        const subject = document.getElementById('subject').value;

        // Get body content from the rich text editor
        let body = '';

        // Since 'description' class auto-initializes the editor,
        // we need to get content from the editor instance
        const bodyTextarea = document.getElementById('body');

        // Try to get content from the editor (assuming it's initialized)
        // The exact method depends on which editor you're using
        if (bodyTextarea && bodyTextarea.nextSibling && bodyTextarea.nextSibling.querySelector) {
            // For some editors, content might be in a sibling element
            const editorContent = bodyTextarea.nextSibling.querySelector('.editor-content, .ck-content, .mce-content-body');
            if (editorContent) {
                body = editorContent.innerHTML || editorContent.textContent;
            }
        }

        // Fallback: try to get from textarea (might work after editor sync)
        if (!body) {
            body = bodyTextarea.value;
        }

        // Another fallback: try getting from any contenteditable div near the textarea
        if (!body) {
            const editableDiv = document.querySelector('.description + div [contenteditable], .description ~ div [contenteditable]');
            if (editableDiv) {
                body = editableDiv.innerHTML;
            }
        }

        const variables = Array.from(document.querySelectorAll('input[name="variables[]"]'))
            .map(input => input.value.trim())
            .filter(value => value !== '');

        // Create sample data for preview
        const sampleData = {};
        variables.forEach(variable => {
            sampleData[variable] = `[${variable}]`;
        });

        // Simple variable replacement for preview
        let previewSubject = subject;
        let previewBody = body;

        Object.keys(sampleData).forEach(key => {
            const regex = new RegExp("{{\\s*\\$" + key + "\\s*}}", "g");
            previewSubject = previewSubject.replace(regex, sampleData[key]);
            previewBody = previewBody.replace(regex, sampleData[key]);
        });

        document.getElementById('preview-content').innerHTML = `
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
    });
});
</script>
@endverbatim
@endpush
