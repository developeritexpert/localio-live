@extends('admin_layout.master')

@section('title', 'Generated Content')

@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Generated Content</h3>
                    <p class="nk-block-des text-soft">
                        Business: <strong>{{ $businessName }}</strong> (ID: {{ $item->resource_id }})
                        &bull; Field: <strong>{{ \App\Models\AiContentQueue::$fieldLabels[$item->content_field] ?? $item->content_field }}</strong>
                    </p>
                </div>
                <div class="nk-block-head-content">
                    <a href="{{ route('content-gen.businesses') }}" class="btn btn-outline-secondary btn-sm">
                        <em class="icon ni ni-arrow-left me-1"></em> Back to Businesses
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <em class="icon ni ni-check-circle me-1"></em> {!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <em class="icon ni ni-cross-circle me-1"></em> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card card-bordered">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2 align-items-center">
                    @php
                        $badgeClass = match($item->status) {
                            'completed' => 'bg-success',
                            'failed'    => 'bg-danger',
                            default     => 'bg-secondary',
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ ucfirst($item->status) }}</span>
                    <span class="text-soft small">Generated {{ $item->updated_at->diffForHumans() }}</span>
                    @if($item->batchJob)
                        <span class="badge bg-light text-muted border">Batch: {{ $item->batchJob->batch_id ?? ('#' . $item->batch_job_id) }} ({{ ucfirst($item->batchJob->provider) }})</span>
                    @endif
                </div>

                <div class="d-flex gap-2">
                    @if($item->status === 'completed' && !empty($item->result))
                        <form action="{{ route('content-gen.apply-result', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apply this generated content directly to the live business record?');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">
                                <em class="icon ni ni-save me-1"></em> Apply to Business Record
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('content-gen.retry-failed') }}" method="POST" class="d-inline" onsubmit="return confirm('Trigger a new generation batch for this field?');">
                        @csrf
                        <input type="hidden" name="business_id" value="{{ $item->resource_id }}">
                        <input type="hidden" name="field" value="{{ $item->content_field }}">
                        <input type="hidden" name="provider" value="{{ $item->batchJob->provider ?? 'openai' }}">
                        <button type="submit" class="btn btn-sm btn-outline-warning">
                            <em class="icon ni ni-reload me-1"></em> Re-generate
                        </button>
                    </form>
                </div>
            </div>

            <div class="card-body">
                @if($item->result)
                    @php
                        $decoded = json_decode($item->result, true);
                    @endphp

                    @if(is_array($decoded))
                        <div class="mb-3">
                            <h6 class="text-muted text-uppercase fs-12px tracking-wider mb-2">Structured Output Preview</h6>
                            <div class="bg-light p-3 rounded border">
                                @if($item->content_field === 'content' && isset($decoded['content']))
                                    <div class="p-2">{!! nl2br(e($decoded['content'])) !!}</div>
                                @elseif($item->content_field === 'faqs' && isset($decoded['faqs']))
                                    <div class="accordion" id="faqPreview">
                                        @foreach($decoded['faqs'] as $i => $faq)
                                            <div class="border-bottom py-2">
                                                <strong>Q: {{ $faq['question'] ?? '' }}</strong>
                                                <p class="mb-0 text-muted mt-1">{{ $faq['answer'] ?? '' }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($item->content_field === 'features' && isset($decoded['features']))
                                    <ul class="list-group list-group-flush">
                                        @foreach($decoded['features'] as $f)
                                            <li class="list-group-item bg-transparent px-0">
                                                <strong>{{ $f['title'] ?? '' }}</strong>: {{ $f['description'] ?? '' }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @elseif($item->content_field === 'pros_cons')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-success"><em class="icon ni ni-check-circle me-1"></em> Pros</h6>
                                            <ul>
                                                @foreach(($decoded['pros'] ?? []) as $pro)
                                                    <li>{{ $pro }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-danger"><em class="icon ni ni-cross-circle me-1"></em> Cons</h6>
                                            <ul>
                                                @foreach(($decoded['cons'] ?? []) as $con)
                                                    <li>{{ $con }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @elseif($item->content_field === 'metatags')
                                    <table class="table table-sm">
                                        <tr><th style="width:150px">Meta Title:</th><td>{{ $decoded['meta_title'] ?? '-' }}</td></tr>
                                        <tr><th>Meta Description:</th><td>{{ $decoded['meta_description'] ?? '-' }}</td></tr>
                                    </table>
                                @else
                                    <pre class="mb-0">{{ json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted text-uppercase fs-12px tracking-wider">Raw JSON Output</span>
                            <button onclick="copyResult()" class="btn btn-xs btn-outline-secondary">
                                <em class="icon ni ni-copy me-1"></em> Copy JSON
                            </button>
                        </div>
                        <pre id="rawOutput" class="p-3 bg-dark text-light rounded" style="white-space:pre-wrap;font-family:monospace;font-size:0.85rem;max-height:400px;overflow-y:auto">{{ $item->result }}</pre>
                    </div>
                @else
                    <div class="alert alert-danger">
                        <h6>Generation Error</h6>
                        <p class="mb-0">{{ $item->error_message ?? 'No result available.' }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<script>
function copyResult() {
    const text = document.getElementById('rawOutput').innerText;
    navigator.clipboard.writeText(text).then(() => alert('Copied to clipboard!'));
}
</script>
@endsection
