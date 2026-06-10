@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg user-review-feedback">
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Vendor Review Feedback</h3>
            </div>
        </div>
    </div>

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            @if($vendorFeedback->isNotEmpty())
                <table class="datatable-init nk-tb-list nk-tb-ulist w-100" data-auto-responsive="false">
                    <thead>
                        <tr class="nk-tb-item nk-tb-head">
                            <th class="nk-tb-col">Vendor</th>
                            <th class="nk-tb-col">Message</th>
                            <th class="nk-tb-col text-center">Marked Inappropriate</th>
                            <th class="nk-tb-col text-center">Status</th>
                            <th class="nk-tb-col">Submitted At</th>
                            <th class="nk-tb-col text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendorFeedback as $feedback)
                            <tr class="nk-tb-item">
                                {{-- Vendor --}}
                                <td class="nk-tb-col" style="vertical-align: middle;">
                                    <div class="user-card">
                                        <div class="user-info">
                                            <span class="tb-lead">{{ $feedback->user->first_name ?? 'Unknown' }}</span>
                                            <span class="tb-sub text-muted d-block">{{ $feedback->user->email ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Message --}}
                                <td class="nk-tb-col" style="vertical-align: middle;">
                                    {{ Str::limit($feedback->message, 100) }}
                                </td>

                                {{-- Inappropriate --}}
                                <td class="nk-tb-col text-center" style="vertical-align: middle;">
                                    <span class="badge {{ $feedback->is_inappropriate ? 'bg-danger' : 'bg-secondary' }}">
                                        {{ $feedback->is_inappropriate ? 'Yes' : 'No' }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="nk-tb-col text-center" style="vertical-align: middle;">
                                    <span class="badge bg-warning text-dark">{{ ucfirst($feedback->status) }}</span>
                                </td>

                                {{-- Submitted At --}}
                                <td class="nk-tb-col" style="vertical-align: middle;">
                                    <div>{{ $feedback->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $feedback->created_at->format('h:i A') }}</small>
                                </td>

                                {{-- Actions --}}
                                <td class="nk-tb-col text-center" style="vertical-align: middle;">
                                    <div class="drodown">
                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                            <em class="icon ni ni-more-h"></em>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end" style="height: auto;">
                                            <ul class="link-list-opt no-bdr">
                                                <li>
                                                    <a href="{{ route('admin.review.feedback.handle', ['id' => $feedback->id, 'action' => 'approve']) }}">
                                                        <em class="icon ni ni-check-circle text-success"></em>
                                                        <span>Approve</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('admin.review.feedback.handle', ['id' => $feedback->id, 'action' => 'reject']) }}">
                                                        <em class="icon ni ni-cross-circle text-danger"></em>
                                                        <span>Reject</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#viewMessageModal{{ $feedback->id }}">
                                                        <em class="icon ni ni-eye"></em>
                                                        <span>View User Review</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            {{-- Modal --}}
                            <div class="modal fade" id="viewMessageModal{{ $feedback->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">User Review</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            @php
                                            $translation = null;

                                            if ($feedback->review && $feedback->review->translations) {
                                                    $translation = $feedback->review->translations->where('language_id', getCurrentLanguageID())->first();
                                                }
                                            @endphp

                                            <b>{{ $translation->title ?? 'No title' }}</b>
                                            <p>{{ $translation->description ?? 'No description' }}</p>

                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-outline-dark" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-5">
                    <button class="btn btn-primary btn-localio">No Feedback Found</button>
                </div>
            @endif
        </div>
    </div>


</div>

@endsection
