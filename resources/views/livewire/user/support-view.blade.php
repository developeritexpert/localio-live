

<div class="col-lg-9 p-0">
    <div class="user_content">
        <div class="uer_nm">
            <h1>Support Ticket</h1>
        </div>

        <div class="crt_main">
            <div class="cart_dv review_dv">
                <div class="crt-lft-top d-flex justify-content-between align-items-start">
                    <div class="d-flex">
                        {{-- <div class="cart_img crt-lft-img">
                            <img src="{{ asset('front/img/support_chat_icon.png') }}" width="30" height="30" alt="Chat Icon">
                        </div> --}}
                        <div class="cart_text">
                            @php
                            $reason = [
                                1 => 'General Inquiry',
                                2 => 'Support',
                                3 => 'Feedback',
                                4 => 'Business Collaboration'
                            ];
                        @endphp
                            <h4>{{ $reason[$ticket->reason_id] }} | {{ $ticket->subject }}</h4>
                            <p>Ticket: <span class="font-mono">{{ $ticket->ticket_id }}</span></p>
                        </div>
                    </div>

                    @php
                        $latestMessage = $ticket->messages->last();
                        $status = $ticket->status;
                        $label = $status === 'closed' ? 'CLOSED' : ($latestMessage && $latestMessage->sent_by === 'user' ? 'OPEN PROCESSING' : 'AWAITING YOUR REPLY');
                        $class = $status === 'closed' ? 'bg-danger text-white' : ($latestMessage && $latestMessage->sent_by === 'user' ? 'bg-success text-white' : 'bg-warning text-dark');
                    @endphp

                    <span class="px-2 py-1 rounded-sm {{ $class }}">{{ $label }}</span>
                </div>

                <hr class="my-3">

                {{-- Messages --}}
                <div class="message-thread space-y-4">
                    @foreach($ticket->messages as $message)
                        @php
                            $isUser = $message->sent_by === 'user';
                            $sender = $isUser ? $ticket->user->first_name . ' ' . $ticket->user->last_name : 'Admin';
                            $mediaUrl = $message->media_url ?? null;
                        @endphp

                        <div class="message-box p-3 border rounded {{ $isUser ? 'bg-light' : 'bg-white' }}">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>{{ $sender }}</strong>
                                <small class="text-muted">{{ $message->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="message-content">
                                @if($message->message)
                                    <p>{!! nl2br(e($message->message)) !!}</p>
                                @endif

                                @if($mediaUrl)
                                {{-- @dd($mediaUrl); --}}
                                    <div class="mt-2">
                                        @if(Str::endsWith(strtolower($mediaUrl), ['.jpg', '.jpeg', '.png', '.gif']))
                                            <img src="{{ $mediaUrl }}" alt="Attachment" class="img-thumbnail" style="max-width: 200px;">
                                        @else
                                            <a href="{{ $mediaUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                <i class="fa fa-download mr-1"></i> Download Attachment
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Reply Form --}}
                <div class="reply-box mt-4">
                    <form wire:submit.prevent="sendReply" enctype="multipart/form-data">
                        <div class="border rounded-lg p-3 bg-white">
                            @if (session()->has('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if (session()->has('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <div class="form-group mb-3">
                                <textarea wire:model.defer="message" class="form-control" rows="5" placeholder="Type your message..."></textarea>
                                @error('message')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">Attach file (optional):</label>
                                <input type="file" wire:model="media" class="form-control">
                                @error('media')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror

                                @if($mediaPreview)
                                    <div class="mt-2">
                                        {{-- @dd($mediaPreview); --}}
                                        <img src="{{ $mediaPreview }}" class="img-thumbnail" style="max-height: 150px;">
                                    </div>
                                @endif

                                <div wire:loading wire:target="media" class="text-muted mt-1">
                                    Uploading file...
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    <span wire:loading.remove>Send Message</span>
                                    <span wire:loading.delay>Sending...</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

