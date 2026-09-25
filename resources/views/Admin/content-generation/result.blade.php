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
                        {{ ($item->resource_type === 'category_feature') ? 'Feature' : 'Business' }}: <strong>{{ $businessName }}</strong> (ID: {{ $item->resource_id }})
                        &bull; Field: <strong>{{ ($item->resource_type === 'category_feature') ? (\App\Http\Controllers\Admin\ContentGenerationController::$featureFieldLabels[$item->content_field] ?? ucfirst($item->content_field)) : (\App\Models\AiContentQueue::$fieldLabels[$item->content_field] ?? $item->content_field) }}</strong>
                    </p>
                </div>
                <div class="nk-block-head-content">
                    @if($item->resource_type === 'category_feature')
                        <a href="{{ route('content-gen.features') }}" class="btn btn-outline-secondary btn-sm">
                            <em class="icon ni ni-arrow-left me-1"></em> Back to Features
                        </a>
                    @else
                        <a href="{{ route('content-gen.businesses') }}" class="btn btn-outline-secondary btn-sm">
                            <em class="icon ni ni-arrow-left me-1"></em> Back to Businesses
                        </a>
                    @endif
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
                                @if($item->content_field === 'short_description')
                                    <div class="p-2 fs-15px text-dark">
                                        {{ $decoded['short_description'] ?? '' }}
                                    </div>
                                @elseif($item->content_field === 'long_description')
                                    <div class="p-2">
                                        {!! $decoded['long_preview_description'] ?? ($decoded['long_description'] ?? '') !!}
                                    </div>
                                @elseif($item->content_field === 'usps')
                                    <ul class="list-unstyled mb-0 p-2">
                                        @foreach(($decoded['usps'] ?? ($decoded['features'] ?? [])) as $usp)
                                            @php $uText = is_array($usp) ? ($usp['title'] ?? ($usp['text'] ?? '')) : (string)$usp; @endphp
                                            <li class="d-flex align-items-center mb-2">
                                                <em class="icon ni ni-check-circle-fill text-success fs-16px me-2"></em>
                                                <span class="fw-medium text-dark">{{ $uText }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @elseif($item->content_field === 'what_is')
                                    <div class="p-2">
                                        {!! $decoded['description'] ?? ($decoded['what_is'] ?? ($decoded['content'] ?? '')) !!}
                                    </div>
                                @elseif($item->content_field === 'pros_cons')
                                    <div class="row g-3 p-2">
                                        <div class="col-md-6">
                                            <h6 class="text-success mb-2"><em class="icon ni ni-plus-circle-fill me-1"></em> Selected Pros</h6>
                                            <ul class="list-unstyled mb-0">
                                                @foreach(($decoded['pros'] ?? []) as $pro)
                                                    <li class="mb-1"><span class="badge bg-success-dim text-success me-1">+</span> {{ $pro }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-danger mb-2"><em class="icon ni ni-minus-circle-fill me-1"></em> Selected Cons</h6>
                                            <ul class="list-unstyled mb-0">
                                                @foreach(($decoded['cons'] ?? []) as $con)
                                                    <li class="mb-1"><span class="badge bg-danger-dim text-danger me-1">-</span> {{ $con }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @elseif($item->content_field === 'services')
                                    <div class="p-2">
                                        @if(!empty($decoded['headline']))
                                            <h5 class="fw-bold mb-2">{{ $decoded['headline'] }}</h5>
                                        @endif
                                        @if(!empty($decoded['top_text']))
                                            <div class="mb-3">
                                                <span class="badge bg-light text-muted mb-1">Top Text (Before Screenshots)</span>
                                                <div class="bg-white p-2 border rounded">{!! $decoded['top_text'] !!}</div>
                                            </div>
                                        @endif
                                        @if(!empty($decoded['bottom_text']))
                                            <div>
                                                <span class="badge bg-light text-muted mb-1">Bottom Text (After Screenshots)</span>
                                                <div class="bg-white p-2 border rounded">{!! $decoded['bottom_text'] !!}</div>
                                            </div>
                                        @endif
                                    </div>
                                @elseif($item->content_field === 'faqs' && isset($decoded['faqs']))
                                    <div class="accordion" id="faqPreview">
                                        @foreach($decoded['faqs'] as $i => $faq)
                                            <div class="border-bottom py-2">
                                                <strong class="text-primary">Q: {{ $faq['question'] ?? '' }}</strong>
                                                <p class="mb-0 text-dark mt-1">{{ $faq['answer'] ?? '' }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($item->content_field === 'categories')
                                    <div class="p-2">
                                        <p class="mb-1"><strong>Primary Category ID:</strong> <span class="badge bg-primary">{{ $decoded['category_id'] ?? 'Not set' }}</span> {{ $decoded['category_name'] ?? '' }}</p>
                                        <p class="mb-0"><strong>Subcategories:</strong> {{ implode(', ', $decoded['subcategory_ids'] ?? []) }}</p>
                                    </div>
                                @elseif($item->content_field === 'active_countries')
                                    <div class="p-2">
                                        <p class="mb-1"><strong>Global (All Countries):</strong> <span class="badge {{ !empty($decoded['active_all_countries']) ? 'bg-success' : 'bg-warning' }}">{{ !empty($decoded['active_all_countries']) ? 'Yes (Global)' : 'No (Specific)' }}</span></p>
                                        @if(!empty($decoded['country_codes']))
                                            <p class="mb-0"><strong>Specific Country Codes:</strong> {{ implode(', ', $decoded['country_codes']) }}</p>
                                        @endif
                                    </div>
                                @elseif($item->content_field === 'rating')
                                    <div class="p-3 text-center">
                                        @php
                                            $ratingVal = $decoded['rating'] ?? ($decoded['value'] ?? null);
                                        @endphp
                                        <div class="display-4 fw-bold text-dark mb-1">
                                            {{ $ratingVal !== null ? number_format((float)$ratingVal, 1) : '--' }} <small class="fs-4 text-muted">/ 5.0</small>
                                        </div>
                                        <div class="fs-18px text-warning mb-2">
                                            @php
                                                $fullStars = floor((float)$ratingVal);
                                                $halfStar = ((float)$ratingVal - $fullStars) >= 0.3;
                                            @endphp
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $fullStars)
                                                    <em class="icon ni ni-star-fill text-warning"></em>
                                                @elseif($halfStar && $i == $fullStars + 1)
                                                    <em class="icon ni ni-star-half text-warning"></em>
                                                @else
                                                    <em class="icon ni ni-star text-muted"></em>
                                                @endif
                                            @endfor
                                        </div>
                                        <p class="text-muted small mb-0">Initial admin rating (active until the business receives 10 approved user reviews).</p>
                                    </div>
                                @elseif($item->content_field === 'content' && isset($decoded['content']))
                                    <div class="p-2">{!! nl2br(e($decoded['content'])) !!}</div>
                                @elseif($item->content_field === 'features' && isset($decoded['features']))
                                    <ul class="list-group list-group-flush">
                                        @foreach($decoded['features'] as $f)
                                            <li class="list-group-item bg-transparent px-0">
                                                <strong>{{ $f['title'] ?? '' }}</strong>: {{ $f['description'] ?? '' }}
                                            </li>
                                        @endforeach
                                    </ul>
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
