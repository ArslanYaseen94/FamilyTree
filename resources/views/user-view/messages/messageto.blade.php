@extends('layouts.user.app')

@section('content')
<div class="main-content right-chat-active">
    <div class="middle-sidebar-bottom">
        <div class="middle-sidebar-left pe-0">
            <div class="container mt-4">
                <div class="card shadow-xss w-100 d-block d-flex border-0 p-4 mb-3">
                    <div class="card-body d-flex align-items-center p-0">
                        <h2 class="fw-700 mb-0 mt-0 font-md text-grey-900">
                            <i class="feather-inbox me-2"></i>{{ __('messages.Messages to You') }}
                        </h2>
                        <span class="badge bg-primary ms-3 fs-6">{{ $messages->total() }}</span>
                    </div>
                </div>

                @if($messages->count() > 0)
                    @foreach($messages as $msg)
                    <div class="card shadow-xss border-0 mb-3">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white me-3"
                                         style="width: 42px; height: 42px; font-size: 16px; flex-shrink: 0;">
                                        @if($msg->sender_id == 0)
                                            A
                                        @elseif($msg->sender)
                                            {{ strtoupper(substr($msg->sender->name, 0, 1)) }}
                                        @else
                                            ?
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="fw-700 mb-0">{{ $msg->subject }}</h6>
                                        <small class="text-muted">
                                            {{ __('messages.From:') }}
                                            <strong>{{ $msg->sender_id == 0 ? 'Admin' : ($msg->sender->name ?? __('messages.Unknown')) }}</strong>
                                        </small>
                                    </div>
                                </div>
                                <small class="text-muted">{{ $msg->created_at->format('d M Y, h:i A') }}</small>
                            </div>

                            <p class="text-grey-600 mt-2 mb-3" style="line-height: 1.6;">
                                {{ Str::limit($msg->body, 200) }}
                            </p>

                            <div class="d-flex justify-content-between align-items-center">
                                @if($msg->replies && $msg->replies->count() > 0)
                                    <span class="badge bg-info text-dark">
                                        <i class="feather-message-circle me-1"></i>{{ $msg->replies->count() }} {{ __('messages.Replies') }}
                                    </span>
                                @else
                                    <span></span>
                                @endif
                                <a href="{{ route('user.messages.show', $msg->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="feather-eye me-1"></i>{{ __('messages.View & Reply') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">
                            {{ __('messages.Showing') }} {{ $messages->firstItem() }} {{ __('messages.to') }} {{ $messages->lastItem() }} {{ __('messages.of') }} {{ $messages->total() }} {{ __('messages.entries') }}
                        </small>
                        {{ $messages->links('pagination::bootstrap-5') }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="feather-inbox" style="font-size: 60px; color: #ccc;"></i>
                        <h5 class="mt-3 text-muted">{{ __('messages.No messages yet') }}</h5>
                        <p class="text-muted">{{ __('messages.Messages sent to you will appear here.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
