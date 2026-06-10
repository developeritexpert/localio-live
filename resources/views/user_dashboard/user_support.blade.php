@section('title', 'User Support | Localio')
@extends('user_dashboard_layout.master')

@section('content')
<div class="col-lg-9 p-0">
    <div class="user_content">
        <div class="uer_nm">
            <h1>
                <i class="fas fa-headset"></i>
                Support Tickets
            </h1>
        </div>

        @if (isset($tickets) && $tickets->isNotEmpty())
            <div class="crt_main">
                @foreach ($tickets as $ticket)
                    @php
                        $latestMessage = $ticket->messages->last();
                        $ticketStatus = $ticket->status;

                        $statusLabel = '';
                        $statusClass = '';
                        $iconClass = 'fas fa-comment-dots'; // default

                        if ($ticketStatus === 'closed') {
                            $statusLabel = 'Closed';
                            $statusClass = 'status-badge status-closed';
                            $iconClass = 'fas fa-check-circle';
                        } elseif ($latestMessage && $latestMessage->sent_by === 'user') {
                            $statusLabel = 'Open Processing';
                            $statusClass = 'status-badge status-processing';
                            $iconClass = 'fas fa-comment-dots';
                        } elseif ($latestMessage && $latestMessage->sent_by === 'admin') {
                            $statusLabel = 'Awaiting Your Reply';
                            $statusClass = 'status-badge status-awaiting';
                            $iconClass = 'fas fa-exclamation-triangle';
                        }
                    @endphp

                    <div class="cart_dv review_dv">
                        <div class="crt-lft-top">
                            <div class="support-icon">
                                <i class="{{ $iconClass }}"></i>
                            </div>
                            <div class="cart_text">
                                <h4>{{ $ticket->reason_text ?? 'Ticket' }}: {{ $ticket->subject }}</h4>
                                <p>Ticket ID: <span class="ticket-id">{{ $ticket->ticket_id }}</span></p>
                                <span class="{{ $statusClass }}">{{ $statusLabel }}</span>
                            </div>
                        </div>

                        <div class="review-btm">
                            <div class="review-btm-lft">
                                <p>
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    Submitted: {{ $ticket->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="review-btm-rgt">
                                <a href="{{ route('user-support-view', ['locale' => session('lang_code', 'en-us'), 'id' => $ticket->ticket_id]) }}" class="blue-btn">
                                    <i class="fas fa-eye"></i>
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state mt-5">
                <div class="support-icon mx-auto mb-4" style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h5>No Support Tickets Yet</h5>
                <p>You haven't submitted any support tickets yet. Need help? Create your first ticket!</p>
                <a href="{{ route('contact', session('lang_code', 'en-us')) }}" class="create-ticket-btn">
                    <i class="fas fa-plus"></i>
                    Create New Ticket
                </a>
            </div>
        @endif
    </div>
</div>

@endsection