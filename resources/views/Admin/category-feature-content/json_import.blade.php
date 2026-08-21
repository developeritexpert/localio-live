@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg">
    <div class="nk-block-head d-flex justify-content-between align-items-center">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">Bulk JSON Import for Category-Feature Content</h4>
            <p class="text-muted">Upload generated content for hundreds of Category + Feature combinations at once.</p>
        </div>
        <a href="{{ route('admin.category-feature-content.index') }}" class="btn btn-outline-light bg-white text-dark">
            <em class="icon ni ni-arrow-left"></em> Back to List
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-icon mb-3">
            <em class="icon ni ni-cross-circle"></em> {{ session('error') }}
        </div>
    @endif

    <div class="row g-4">
        <!-- Import Form -->
        <div class="col-lg-7">
            <div class="card card-bordered h-100">
                <div class="card-inner">
                    <form action="{{ route('admin.category-feature-content.json-import-process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">
                            <!-- Default Language -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="default_lang_id">Default Target Language</label>
                                    <div class="form-control-wrap">
                                        <select name="default_lang_id" id="default_lang_id" class="form-select">
                                            @foreach($languages as $lang)
                                                <option value="{{ $lang->id }}" {{ $lang->lang_code == 'en-us' ? 'selected' : '' }}>
                                                    {{ $lang->lang_code }} - {{ $lang->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <small class="text-muted">Used if a row in the JSON doesn't specify a <code>lang_code</code> or <code>lang_id</code>.</small>
                                </div>
                            </div>

                            <!-- Upload JSON File -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="json_file">Upload JSON File</label>
                                    <div class="form-control-wrap">
                                        <input type="file" name="json_file" id="json_file" class="form-control" accept=".json,.txt">
                                    </div>
                                </div>
                            </div>

                            <!-- OR Paste JSON Direct -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="json_data">Or Paste JSON Content Below</label>
                                    <div class="form-control-wrap">
                                        <textarea name="json_data" id="json_data" class="form-control" rows="12" placeholder='[
  {
    "category_slug": "shared-hosting",
    "feature_name": "Noise Cancellation",
    "description": "<p>Discover the best shared hosting providers offering noise cancellation...</p>",
    "text_sections": [
      {
        "h2_title": "Why choose shared hosting with noise cancellation?",
        "h2_text": "<p>Content goes here...</p>",
        "sub_sections": [
          {
            "h3_title": "Key Benefits",
            "h3_text": "<p>Sub-content here...</p>"
          }
        ]
      }
    ]
  }
]' style="font-family: monospace; font-size: 13px;"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100" style="background:#F9633B; border-color:#F9633B;">
                                    <em class="icon ni ni-upload-cloud"></em> Process & Import JSON Content
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sample Schema & AI Prompt Instructions -->
        <div class="col-lg-5">
            <div class="card card-bordered h-100 bg-light">
                <div class="card-inner">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="title text-primary mb-0"><em class="icon ni ni-code"></em> AI Prompt JSON Template</h6>
                        <button type="button" class="btn btn-xs btn-outline-primary" onclick="copyTemplate()">
                            <em class="icon ni ni-copy"></em> Copy Schema
                        </button>
                    </div>
                    <p class="small text-muted mb-3">
                        Use this exact structure with your AI prompt (ChatGPT/Claude/Gemini). You can match by Category ID/Slug/Name and Feature ID/Slug/Name.
                    </p>

                    <div class="position-relative">
                        <pre id="jsonTemplateBlock" class="p-3 bg-dark text-white rounded small" style="max-height: 440px; overflow-y: auto; font-size: 11.5px; line-height: 1.5;">[
  {
    "category_slug": "shared-hosting",
    "feature_name": "Noise Cancellation",
    "lang_code": "en-us",
    "description": "&lt;p&gt;Overview paragraph explaining how shared hosting providers handle noise cancellation.&lt;/p&gt;",
    "text_sections": [
      {
        "h2_title": "Why noise cancellation matters for shared hosting",
        "h2_text": "&lt;p&gt;Detailed explanation of how server noise isolation improves performance.&lt;/p&gt;",
        "sub_sections": [
          {
            "h3_title": "Key Advantages",
            "h3_text": "&lt;p&gt;Bullet points or insights on reliability.&lt;/p&gt;"
          },
          {
            "h3_title": "How to choose the right provider",
            "h3_text": "&lt;p&gt;Checklist for buyers.&lt;/p&gt;"
          }
        ]
      },
      {
        "h2_title": "Top features to compare",
        "h2_text": "&lt;p&gt;Comparison criteria for this category and feature.&lt;/p&gt;"
      }
    ]
  }
]</pre>
                    </div>

                    <div class="alert alert-info alert-icon mt-3 py-2 small">
                        <em class="icon ni ni-info"></em>
                        <strong>Tip:</strong> If <code>category_slug</code> or <code>feature_name</code> exists, the system automatically links them. Existing records will be updated safely.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyTemplate() {
    const text = document.getElementById('jsonTemplateBlock').innerText;
    navigator.clipboard.writeText(text).then(() => {
        alert('JSON schema copied to clipboard! You can paste it into your AI prompt.');
    });
}
</script>
@endsection
