@extends('admin_layout.master')

@section('title', 'Review Translations')

@section('content')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between align-items-center">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Review Translations</h3>
                    <p class="nk-block-des text-soft">
                        Automated translation engine for published reviews using OpenAI Batch API (gpt-5.6-luna) and manual ChatGPT workflow.
                    </p>
                </div>
                <div class="nk-block-head-content d-flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('translations.reviews.dispatch-batch') }}" class="d-inline" onsubmit="return confirm('Dispatch batch for up to 500 WAITING review translations?');">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm" {{ ($counts['waiting'] ?? 0) == 0 ? 'disabled' : '' }}>
                            <em class="icon ni ni-send me-1"></em> Dispatch Batch ({{ $counts['waiting'] ?? 0 }} Waiting)
                        </button>
                    </form>
                    <a href="{{ route('translations.batch-jobs') }}" class="btn btn-outline-primary btn-sm">
                        <em class="icon ni ni-layers me-1"></em> View Batch Jobs
                    </a>
                    <a href="{{ route('translations.poll-status') }}" class="btn btn-info btn-sm">
                        <em class="icon ni ni-reload me-1"></em> Poll Status
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
                <em class="icon ni ni-alert-circle me-1"></em> {!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Status Filter Tabs -->
        <ul class="nav nav-tabs mb-3 border-bottom">
            <li class="nav-item">
                <a class="nav-link {{ ($statusFilter ?? 'all') === 'all' ? 'active' : '' }}" href="{{ route('translations.reviews', array_merge(request()->except(['status', 'page']), ['status' => 'all'])) }}">
                    All <span class="badge bg-light text-dark ms-1">{{ $counts['all'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ ($statusFilter ?? '') === 'waiting' ? 'active' : '' }}" href="{{ route('translations.reviews', array_merge(request()->except(['status', 'page']), ['status' => 'waiting'])) }}">
                    Waiting <span class="badge bg-warning ms-1">{{ $counts['waiting'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ ($statusFilter ?? '') === 'completed' ? 'active' : '' }}" href="{{ route('translations.reviews', array_merge(request()->except(['status', 'page']), ['status' => 'completed'])) }}">
                    Completed <span class="badge bg-success ms-1">{{ $counts['completed'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ ($statusFilter ?? '') === 'outdated' ? 'active' : '' }}" href="{{ route('translations.reviews', array_merge(request()->except(['status', 'page']), ['status' => 'outdated'])) }}">
                    Outdated <span class="badge bg-secondary ms-1">{{ $counts['outdated'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ ($statusFilter ?? '') === 'failed' ? 'active' : '' }}" href="{{ route('translations.reviews', array_merge(request()->except(['status', 'page']), ['status' => 'failed'])) }}">
                    Failed <span class="badge bg-danger ms-1">{{ $counts['failed'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ ($statusFilter ?? '') === 'needs_review' ? 'active' : '' }}" href="{{ route('translations.reviews', array_merge(request()->except(['status', 'page']), ['status' => 'needs_review'])) }}">
                    Needs Review <span class="badge bg-dark ms-1">{{ $counts['needs_review'] ?? 0 }}</span>
                </a>
            </li>
        </ul>

        <!-- Filter bar -->
        <div class="card card-bordered mb-3">
            <div class="card-inner py-3">
                <form method="GET" action="{{ route('translations.reviews') }}" class="row g-2 align-items-end">
                    <input type="hidden" name="status" value="{{ $statusFilter ?? 'all' }}">
                    <div class="col-md-3">
                        <label class="form-label fs-12px mb-1">Search Review Text / ID</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Review ID or keyword..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-12px mb-1">Source Locale</label>
                        <select name="source_locale" class="form-select form-select-sm">
                            <option value="">All Source Locales</option>
                            @foreach($locales as $loc)
                                <option value="{{ $loc }}" {{ request('source_locale') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-12px mb-1">Target Locale</label>
                        <select name="target_locale" class="form-select form-select-sm">
                            <option value="">All Target Locales</option>
                            @foreach($locales as $loc)
                                <option value="{{ $loc }}" {{ request('target_locale') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        <a href="{{ route('translations.reviews', ['status' => $statusFilter ?? 'all']) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Translations Queue Table -->
        <div class="card card-bordered card-preview">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light fs-12px text-muted">
                        <tr>
                            <th style="width: 80px;">Review ID</th>
                            <th style="min-width: 140px;">Locales</th>
                            <th style="min-width: 250px;">Original Content</th>
                            <th style="min-width: 250px;">Translated Content</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width: 90px;" class="text-center">Retries</th>
                            <th style="width: 100px;" class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($translations as $tr)
                            <tr>
                                <td>
                                    <a href="{{ route('review-edit', $tr->review_id ?? $tr->reviews_id ?? 0) }}" target="_blank" class="fw-bold text-primary">
                                        #{{ $tr->review_id ?? $tr->reviews_id }}
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 fs-12px">
                                        <span class="badge bg-outline-secondary">{{ $tr->source_locale ?? 'en-US' }}</span>
                                        <em class="icon ni ni-arrow-right text-muted"></em>
                                        <span class="badge bg-primary">{{ $tr->target_locale ?? '—' }}</span>
                                    </div>
                                    @if($tr->batch_id)
                                        <div class="text-muted fs-11px mt-1">Batch #{{ $tr->batch_id }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold fs-12px text-dark">{{ \Illuminate\Support\Str::limit($tr->review->title ?? $tr->review->summary ?? '—', 50) }}</div>
                                    <div class="text-muted fs-11px">{{ \Illuminate\Support\Str::limit($tr->review->review ?? $tr->review->description ?? '', 120) }}</div>
                                </td>
                                <td>
                                    @if($tr->translated_title || $tr->translated_text || $tr->title || $tr->description)
                                        <div class="fw-bold fs-12px text-dark">{{ \Illuminate\Support\Str::limit($tr->translated_title ?? $tr->title, 50) }}</div>
                                        <div class="text-muted fs-11px">{{ \Illuminate\Support\Str::limit($tr->translated_text ?? $tr->description, 120) }}</div>
                                    @else
                                        <span class="text-muted fst-italic fs-11px">Pending translation...</span>
                                    @endif
                                </td>
                                <td>
                                    @if($tr->status === 'COMPLETED')
                                        <span class="badge bg-success">COMPLETED</span>
                                    @elseif($tr->status === 'WAITING')
                                        <span class="badge bg-warning">WAITING</span>
                                    @elseif($tr->status === 'OUTDATED')
                                        <span class="badge bg-secondary">OUTDATED</span>
                                    @elseif($tr->status === 'FAILED')
                                        <span class="badge bg-danger">FAILED</span>
                                    @elseif($tr->status === 'NEEDS_REVIEW')
                                        <span class="badge bg-dark">NEEDS REVIEW</span>
                                    @else
                                        <span class="badge bg-light text-dark">{{ $tr->status ?? 'ORIGINAL' }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-muted">{{ $tr->retry_count ?? 0 }} / 3</span>
                                </td>
                                <td class="text-end">
                                    @if(in_array($tr->status, ['FAILED', 'NEEDS_REVIEW', 'OUTDATED']))
                                        <form method="POST" action="{{ route('translations.reviews.retry') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="translation_id" value="{{ $tr->id }}">
                                            <button type="submit" class="btn btn-xs btn-outline-primary" title="Requeue Translation">
                                                <em class="icon ni ni-reload"></em> Retry
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    No review translations found in this category.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($translations->hasPages())
                <div class="card-inner py-3 border-top">
                    {{ $translations->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection