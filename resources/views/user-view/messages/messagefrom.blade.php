@extends('layouts.user.app')

@section('content')
<div class="main-content right-chat-active">
    <div class="middle-sidebar-bottom">
        <div class="middle-sidebar-left pe-0">

            <div class="card shadow-xss w-100 d-block d-flex border-0 p-4 mb-3">
                <div class="card-body d-flex align-items-center p-0">
                    <h2 class="fw-700 mb-0 mt-0 font-md text-grey-900">
                        <i class="feather-mail me-2"></i>{{ __('messages.Messages to All Members') }}
                    </h2>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow rounded-4">
                <div class="card-header bg-primary text-white rounded-top-4 p-4">
                    <h5 class="mb-0"><i class="feather-send me-2"></i>{{ __('messages.Send Email to Members') }}</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('user.messages.send-email') }}" method="POST">
                        @csrf

                        {{-- Member Selection --}}
                        <div class="mb-4">
                            <label class="form-label fw-600">
                                {{ __('messages.Select Members') }}
                                <span class="text-danger">*</span>
                            </label>

                            @if($members->isEmpty())
                                <div class="alert alert-warning">
                                    {{ __('messages.No members with email addresses found in your family trees.') }}
                                </div>
                            @else
                                <div class="mb-2 d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">
                                        <i class="feather-check-square me-1"></i>Select All
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">
                                        <i class="feather-square me-1"></i>Deselect All
                                    </button>
                                    <span class="ms-auto text-muted small align-self-center">
                                        <span id="selectedCount">0</span> selected
                                    </span>
                                </div>

                                <div class="border rounded p-3" style="max-height: 260px; overflow-y: auto; background:#fafafa;">
                                    @foreach($members as $member)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input member-checkbox"
                                                   type="checkbox"
                                                   name="member_ids[]"
                                                   value="{{ $member->id }}"
                                                   id="member_{{ $member->id }}"
                                                   {{ is_array(old('member_ids')) && in_array($member->id, old('member_ids')) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="member_{{ $member->id }}">
                                                <strong>{{ $member->firstname }} {{ $member->lastname }}</strong>
                                                <span class="text-muted ms-2 small">{{ $member->email }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Subject --}}
                        <div class="mb-3">
                            <label for="subject" class="form-label fw-600">
                                {{ __('messages.Subject') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="subject"
                                   id="subject"
                                   class="form-control @error('subject') is-invalid @enderror"
                                   placeholder="{{ __('messages.Enter email subject') }}"
                                   value="{{ old('subject') }}"
                                   required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Body --}}
                        <div class="mb-4">
                            <label for="body" class="form-label fw-600">
                                {{ __('messages.Message') }} <span class="text-danger">*</span>
                            </label>
                            <textarea name="body"
                                      id="body"
                                      rows="7"
                                      class="form-control @error('body') is-invalid @enderror"
                                      placeholder="{{ __('messages.Write your message here...') }}"
                                      required>{{ old('body') }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-5" {{ $members->isEmpty() ? 'disabled' : '' }}>
                                <i class="feather-send me-2"></i>{{ __('messages.Send Email') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Sent Messages History --}}
            @if(isset($sentMessages) && $sentMessages->count() > 0)
            <div class="card shadow rounded-4 mt-4">
                <div class="card-header bg-dark text-white rounded-top-4 p-4">
                    <h5 class="mb-0"><i class="feather-list me-2"></i>{{ __('messages.Sent Messages History') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.Subject') }}</th>
                                    <th>{{ __('messages.Message') }}</th>
                                    <th>{{ __('messages.Sent At') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sentMessages as $index => $msg)
                                <tr>
                                    <td>{{ $sentMessages->firstItem() + $index }}</td>
                                    <td><strong>{{ $msg->subject }}</strong></td>
                                    <td>{{ Str::limit($msg->body, 80) }}</td>
                                    <td><small>{{ $msg->created_at->format('d M Y, h:i A') }}</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">
                        {{ $sentMessages->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<script>
    const checkboxes = document.querySelectorAll('.member-checkbox');
    const countEl    = document.getElementById('selectedCount');

    function updateCount() {
        countEl.textContent = document.querySelectorAll('.member-checkbox:checked').length;
    }

    document.getElementById('selectAll')?.addEventListener('click', () => {
        checkboxes.forEach(cb => cb.checked = true);
        updateCount();
    });

    document.getElementById('deselectAll')?.addEventListener('click', () => {
        checkboxes.forEach(cb => cb.checked = false);
        updateCount();
    });

    checkboxes.forEach(cb => cb.addEventListener('change', updateCount));
    updateCount();
</script>
@endsection
