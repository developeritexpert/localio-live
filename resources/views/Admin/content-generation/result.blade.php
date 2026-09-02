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
                        Field: <strong>{{ \App\Models\AiContentQueue::$fieldLabels[$item->content_field] ?? $item->content_field }}</strong>
                        &bull; Business ID: <strong>{{ $item->resource_id }}</strong>
                    </p>
                </div>
                <div class="nk-block-head-content">
                    <a href="{{ route('content-gen.businesses') }}" class="btn btn-outline-secondary btn-sm">
                        <em class="icon ni ni-arrow-left me-1"></em> Back
                    </a>
                </div>
            </div>
        </div>

        <div class="card card-bordered">
            <div class="card-header">
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
                </div>
            </div>
            <div class="card-body">
                @if($item->result)
                    <pre class="p-3 bg-light rounded" style="white-space:pre-wrap;font-family:inherit;font-size:0.9rem">{{ $item->result }}</pre>
                    <div class="mt-3 d-flex gap-2">
                        <button onclick="copyResult()" class="btn btn-sm btn-outline-primary">
                            <em class="icon ni ni-copy me-1"></em> Copy
                        </button>
                    </div>
                @else
                    <p class="text-danger">{{ $item->error_message ?? 'No result available.' }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
<script>
function copyResult() {
    const text = document.querySelector('pre').innerText;
    navigator.clipboard.writeText(text).then(() => alert('Copied to clipboard!'));
}
</script>
@endsection
