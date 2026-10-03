@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Mail Template: <code>{{ $mailTemplate->key }}</code></h4>
            </div>
            <div class="nk-block-head-content d-flex gap-2">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendTestMailModal">
                    <em class="icon ni ni-send me-1"></em> Send Test Email
                </button>
                <a href="{{ route('mail-templates.edit', $mailTemplate) }}" class="btn" style="
                background-color: #F9633B;
                color: white;"> Edit
                </a>
            </div>
        </div>

        <div class="row g-gs">
            <!-- Template Information -->
            <div class="col-md-6">
                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Template Information</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-inner">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label"><strong>Template Key:</strong></label>
                                        <div class="form-control-wrap">
                                            <div class="form-control-plaintext bg-lighter border rounded p-2">
                                                <code>{{ $mailTemplate->key }}</code>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label"><strong>Status:</strong></label>
                                        <div class="form-control-wrap">
                                            <div class="form-control-plaintext bg-lighter border rounded p-2">
                                                @if($mailTemplate->status == 'active')
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label"><strong>Available Variables:</strong></label>
                                        <div class="form-control-wrap">
                                            <div class="form-control-plaintext bg-lighter border rounded p-2">
                                                @if($mailTemplate->variables && count($mailTemplate->variables) > 0)
                                                    @foreach($mailTemplate->variables as $variable)
                                                        <span class="badge badge-info me-1">${{ $variable }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">No variables defined</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label"><strong>Translations:</strong></label>
                                        <div class="form-control-wrap">
                                            <div class="form-control-plaintext bg-lighter border rounded p-2">
                                                <span class="badge badge-secondary">{{ $mailTemplate->translations->count() }} translations</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label"><strong>Created:</strong></label>
                                        <div class="form-control-wrap">
                                            <div class="form-control-plaintext bg-lighter border rounded p-2">
                                                {{ $mailTemplate->created_at->format('Y-m-d H:i:s') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Controls -->
            <div class="col-md-6">
                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Preview Controls</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-inner">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label" for="language-selector">Select Language</label>
                                        <div class="form-control-wrap">
                                            <select class="form-select" id="language-selector">
                                                @foreach($languages as $language)
                                                    <option value="{{ $language->id }}" {{ $language->id == 1 ? 'selected' : '' }}>
                                                        {{ $language->name }}
                                                        @if($language->id == 1) (Default) @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                @if($mailTemplate->variables && count($mailTemplate->variables) > 0)
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label">Sample Data for Variables</label>
                                        @foreach($mailTemplate->variables as $variable)
                                            <div class="form-control-wrap mb-2">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">${{ $variable }}</span>
                                                    </div>
                                                    <input type="text" class="form-control sample-variable"
                                                           data-variable="{{ $variable }}"
                                                           placeholder="Sample value for {{ $variable }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <div class="col-12">
                                    <button type="button" class="btn btn-primary" id="preview-btn">
                                        <em class="icon ni ni-eye"></em> Preview Template
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Template Translations -->
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Template Translations</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-inner">
                            @if($mailTemplate->translations->count() > 0)
                                <div class="accordion" id="translationsAccordion" style="border:none;">
                                    @foreach($mailTemplate->translations as $index => $translation)
                                        @php
                                            $language = $languages->find($translation->lang_id);
                                        @endphp
                                        <div class="accordion-item  rounded mb-2">
                                            <h3 class="accordion-header" id="heading{{ $translation->lang_id }}">
                                                <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }} bg-light"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapse{{ $translation->lang_id }}"
                                                        aria-expanded="{{ $index == 0 ? 'true' : 'false' }}">
                                                    {{ $language->name ?? 'Unknown Language' }}
                                                    @if($translation->lang_id == 1)
                                                        <span class="badge badge-primary ms-2">Default</span>
                                                    @endif
                                                </button>
                                            </h3>
                                            <div id="collapse{{ $translation->lang_id }}"
                                                 class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                                                 data-bs-parent="#translationsAccordion" >
                                                <div class="accordion-body">
                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label class="form-label"><strong>Subject:</strong></label>
                                                                <div class="form-control-wrap">
                                                                    <div class="form-control-plaintext bg-lighter border rounded p-2">
                                                                        {{ $translation->subject }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label class="form-label"><strong>Body:</strong></label>
                                                                <div class="form-control-wrap">
                                                                    <div class="form-control-plaintext bg-lighter border rounded p-3" >{!! $translation->body !!}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <a href="{{ route('mail-templates.edit', ['mailTemplate' => $mailTemplate, 'lang_id' => $translation->lang_id]) }}"
                                                               class="btn btn-warning btn-sm">
                                                                <em class="icon ni ni-edit"></em> Edit This Translation
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-muted">No translations found for this template.</p>
                                    <a href="{{ route('mail-templates.edit', $mailTemplate) }}" class="btn btn-primary">
                                        <em class="icon ni ni-plus"></em> Add Translation
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Template Preview -->
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner-group">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Template Preview</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-inner">
                            <div id="preview-content">
                                <p class="text-muted">Select a language and click "Preview Template" to see how the template will look with sample data.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Send Test Email Modal -->
    <div class="modal fade" id="sendTestMailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Test Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="testMailAlert" class="d-none alert mb-3"></div>
                    <form id="sendTestMailForm">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label" for="test_recipient_email">Recipient Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="test_recipient_email" name="email" placeholder="e.g. your-email@example.com" value="{{ auth()->user()->email ?? '' }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label" for="test_lang_id">Language</label>
                            <select class="form-select" id="test_lang_id" name="lang_id">
                                @foreach($languages as $language)
                                    <option value="{{ $language->id }}">{{ $language->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-note text-muted">
                            <small>This will generate sample data for all template variables and send a live email.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btnSubmitTestEmail">
                        <span id="testEmailBtnText">Send Email</span>
                        <span id="testEmailSpinner" class="spinner-border spinner-border-sm d-none ms-1" role="status"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const previewBtn = document.getElementById('preview-btn'); // Fixed: Added variable name
    const languageSelector = document.getElementById('language-selector');
    const previewContent = document.getElementById('preview-content');

    if (!previewBtn) {
        console.error('Preview button not found');
        return;
    }

    previewBtn.addEventListener('click', function() {
        const langId = languageSelector.value;
        const sampleData = {};

        // Collect sample data from inputs
        document.querySelectorAll('.sample-variable').forEach(input => {
            const variable = input.getAttribute('data-variable');
            const value = input.value.trim() || `[${variable}]`;
            sampleData[variable] = value;
        });

        // Show loading
        previewContent.innerHTML = '<div class="text-center"><em class="icon ni ni-loading"></em> Loading preview...</div>';

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('CSRF token not found. Make sure you have <meta name="csrf-token" content="{{ csrf_token() }}"> in your layout.');
            previewContent.innerHTML = '<div class="alert alert-danger">CSRF token not found. Please refresh the page.</div>';
            return;
        }

        // Make AJAX request to preview endpoint
        const previewUrl = '{{ route("mail-templates.preview", $mailTemplate) }}';

        fetch(`${previewUrl}?lang_id=${langId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken.getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                sample_data: sampleData
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                previewContent.innerHTML = `
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label"><strong>Subject:</strong></label>
                                <div class="form-control-wrap">
                                    <div class="form-control-plaintext bg-lighter border rounded p-2">
                                        ${data.subject}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label"><strong>Body:</strong></label>
                                <div class="form-control-wrap">
                                    <div class="form-control-plaintext bg-lighter border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                        ${data.body}
                                    </div>
                                </div>
                            </div>
                        </div>
                        ${data.html_body ? `
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label"><strong>HTML Preview:</strong></label>
                                <div class="form-control-wrap">
                                    <iframe srcdoc="${data.html_body.replace(/"/g, '&quot;')}"
                                            style="width: 100%; height: 300px; border: 1px solid #ddd; border-radius: 4px;">
                                    </iframe>
                                </div>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                `;
            } else {
                previewContent.innerHTML = `<div class="alert alert-danger">Error: ${data.error || 'Unknown error occurred'}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            previewContent.innerHTML = `<div class="alert alert-danger">Error loading preview: ${error.message}</div>`;
        });
    });

    // Auto-fill sample data based on variable names
    document.querySelectorAll('.sample-variable').forEach(input => {
        const variable = input.getAttribute('data-variable');

        // Provide some default sample values based on common variable names
        const sampleValues = {
            'name': 'John Doe',
            'email': 'john@example.com',
            'otp': '123456',
            'password': 'newpassword123',
            'token': 'abc123xyz789',
            'username': 'johndoe',
            'phone': '+1234567890',
            'company': 'Example Company',
            'url': 'https://example.com',
            'link': 'https://example.com/reset',
            'code': 'VERIFY123',
            'amount': '$100.00',
            'date': new Date().toLocaleDateString(),
            'time': new Date().toLocaleTimeString()
        };

        if (sampleValues[variable.toLowerCase()]) {
            input.placeholder = `e.g., ${sampleValues[variable.toLowerCase()]}`;
        }
    });

    // Send Test Email AJAX
    const btnSubmitTestEmail = document.getElementById('btnSubmitTestEmail');
    if (btnSubmitTestEmail) {
        btnSubmitTestEmail.addEventListener('click', function() {
            const emailInput = document.getElementById('test_recipient_email');
            const langSelect = document.getElementById('test_lang_id');
            const alertBox = document.getElementById('testMailAlert');
            const btnText = document.getElementById('testEmailBtnText');
            const spinner = document.getElementById('testEmailSpinner');

            if (!emailInput.value || !emailInput.checkValidity()) {
                alertBox.className = 'alert alert-danger';
                alertBox.textContent = 'Please enter a valid email address.';
                alertBox.classList.remove('d-none');
                return;
            }

            btnSubmitTestEmail.disabled = true;
            btnText.textContent = 'Sending...';
            spinner.classList.remove('d-none');
            alertBox.classList.add('d-none');

            fetch('{{ route('mail-templates.send-test', $mailTemplate->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    email: emailInput.value,
                    lang_id: langSelect.value
                })
            })
            .then(res => res.json())
            .then(data => {
                btnSubmitTestEmail.disabled = false;
                btnText.textContent = 'Send Email';
                spinner.classList.add('d-none');
                alertBox.classList.remove('d-none');

                if (data.success) {
                    alertBox.className = 'alert alert-success';
                    alertBox.textContent = data.message || 'Test email sent successfully!';
                } else {
                    alertBox.className = 'alert alert-danger';
                    alertBox.textContent = data.message || 'Failed to send test email.';
                }
            })
            .catch(err => {
                btnSubmitTestEmail.disabled = false;
                btnText.textContent = 'Send Email';
                spinner.classList.add('d-none');
                alertBox.className = 'alert alert-danger';
                alertBox.textContent = 'Network or server error: ' + err.message;
                alertBox.classList.remove('d-none');
            });
        });
    }
});
</script>
@endpush
