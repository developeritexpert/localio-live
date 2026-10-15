@extends('admin_layout.master')

@section('title', 'Batch #' . $job->id . ' Reviews')

@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between align-items-center">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">
                        <em class="icon ni ni-layers text-primary me-2"></em> Batch Items &bull; <code class="fs-18px">{{ $job->batch_id }}</code>
                    </h3>
                    <p class="nk-block-des text-soft">
                        Reviews evaluated in Layer 3 batch job #{{ $job->id }} &bull; Model: <strong>{{ config('openai.review_ai_model', 'gpt-5.6-luna') }}</strong>
                        &bull; Status: <strong>{{ ucfirst($job->status) }}</strong>
                    </p>
                </div>
                <div class="nk-block-head-content d-flex gap-2">
                    <a href="{{ route('admin.reviews.batches.download', $job) }}" class="btn btn-outline-primary btn-sm">
                        <em class="icon ni ni-download me-1"></em> Download Input JSON
                    </a>
                    <a href="{{ route('admin.reviews.batches') }}" class="btn btn-secondary btn-sm">
                        <em class="icon ni ni-arrow-left me-1"></em> Back to Batches
                    </a>
                </div>
            </div>
        </div>

        <div class="card card-bordered card-stretch">
            <div class="card-inner p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Business</th>
                                <th>Reviewer</th>
                                <th>Rating</th>
                                <th>Review Excerpt</th>
                                <th>Locale (Sub / Det)</th>
                                <th>AI Decision</th>
                                <th>Current Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reviews as $review)
                                @php
                                    $translation = $review->translations->first();
                                @endphp
                                <tr>
                                    <td><strong>#{{ $review->id }}</strong></td>
                                    <td>
                                        <div class="fw-bold">{{ $review->business?->translations?->first()?->name ?? 'Unknown Business' }}</div>
                                        <span class="fs-11px text-soft">{{ $review->business?->category?->name ?? 'Software' }}</span>
                                    </td>
                                    <td>
                                        @if($review->user)
                                            <div>{{ $review->user->first_name }} {{ $review->user->last_name }}</div>
                                            <span class="fs-11px text-soft">{{ $review->user->email }}</span>
                                        @else
                                            <span class="text-muted">Anonymous</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-warning-dim text-warning fw-bold">
                                            ★ {{ $review->rating }}
                                        </span>
                                    </td>
                                    <td style="max-width: 300px;">
                                        @if($translation?->title)
                                            <div class="fw-bold fs-12px text-truncate">{{ $translation->title }}</div>
                                        @endif
                                        <div class="fs-12px text-soft text-truncate" title="{{ $translation?->description }}">
                                            {{ Str::limit($translation?->description ?? 'No text', 80) }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $review->submitted_locale ?? 'en-US' }}</span>
                                        @if($review->detected_locale && $review->detected_locale !== $review->submitted_locale)
                                            <em class="icon ni ni-arrow-right text-muted mx-1"></em>
                                            <span class="badge bg-info-dim text-info">{{ $review->detected_locale }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($review->localio_ai_decision === 'PASS')
                                            <span class="badge bg-success-dim text-success"><em class="icon ni ni-check me-1"></em>PASS</span>
                                        @elseif($review->localio_ai_decision === 'HUMAN_REVIEW')
                                            <span class="badge bg-danger-dim text-danger"><em class="icon ni ni-shield-alert me-1"></em>HUMAN REVIEW</span>
                                        @else
                                            <span class="badge bg-light text-muted">{{ $review->localio_ai_decision ?? 'PENDING' }}</span>
                                        @endif

                                        @if(!empty($review->localio_ai_flags))
                                            <div class="fs-10px text-danger mt-1">
                                                @foreach((array)$review->localio_ai_flags as $flag)
                                                    <span class="badge bg-danger text-white p-0 px-1">{{ $flag }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($review->status === \App\Models\Review::STATUS_PUBLISHED)
                                            <span class="badge bg-success"><em class="icon ni ni-check-circle me-1"></em>Published</span>
                                        @elseif($review->status === \App\Models\Review::STATUS_PENDING_REVIEW)
                                            <a href="{{ route('admin.reviews.pending') }}#review-{{ $review->id }}" class="badge bg-warning text-dark">
                                                <em class="icon ni ni-alert-circle me-1"></em>Pending Review
                                            </a>
                                        @elseif($review->status === \App\Models\Review::STATUS_PROCESSING)
                                            <span class="badge bg-info"><em class="icon ni ni-loader me-1"></em>Processing</span>
                                        @elseif($review->status === \App\Models\Review::STATUS_REJECTED)
                                            <span class="badge bg-danger">Rejected</span>
                                        @elseif($review->status === \App\Models\Review::STATUS_REMOVED)
                                            <span class="badge bg-dark">Removed</span>
                                        @else
                                            <span class="badge bg-light text-dark">{{ $review->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-soft">
                                        No reviews found associated with this batch ID.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($reviews->hasPages())
                <div class="card-footer border-top py-2">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
