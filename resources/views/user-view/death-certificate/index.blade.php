@extends('layouts.user.app')

@section('content')
<div class="main-content right-chat-active">
    <div class="middle-sidebar-bottom">
        <div class="middle-sidebar-left pe-0">
            <div class="container mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0"><i class="feather-users me-2"></i>{{ __('messages.Deceased Members') }}</h4>
                    <span class="badge bg-secondary fs-6">{{ $deceasedMembers->total() }} {{ __('messages.Members') }}</span>
                </div>

                @if($deceasedMembers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.Photo') }}</th>
                                <th>{{ __('messages.Name') }}</th>
                                <th>{{ __('messages.Family') }}</th>
                                <th>{{ __('messages.Birth Date') }}</th>
                                <th>{{ __('messages.Death Date') }}</th>
                                <th>{{ __('messages.Death Place') }}</th>
                                <th>{{ __('messages.Age') }}</th>
                                <th>{{ __('messages.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deceasedMembers as $index => $member)
                            <tr>
                                <td>{{ $deceasedMembers->firstItem() + $index }}</td>
                                <td>
                                    @if($member->photo)
                                        <img src="{{ asset('assets/front-end/Memberimgs/' . $member->photo) }}"
                                             class="rounded-circle" width="45" height="45"
                                             style="object-fit: cover; border: 2px solid #6c757d;"
                                             alt="{{ $member->firstname }}">
                                    @else
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white"
                                             style="width: 45px; height: 45px; font-size: 18px;">
                                            {{ strtoupper(substr($member->firstname, 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $member->firstname }} {{ $member->lastname }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $member->gender == 1 ? __('messages.Male') : ($member->gender == 2 ? __('messages.Female') : __('messages.Other')) }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $member->family->familyid ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $member->birthdate ?? '—' }}</td>
                                <td>{{ $member->deathdate ?? '—' }}</td>
                                <td>{{ $member->deathplace ?? '—' }}</td>
                                <td>
                                    @if($ageFormatted = $member->getAgeAtDeathFormatted())
                                        <span class="badge bg-dark">{{ $ageFormatted }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('user.death.certificate', $member->id) }}"
                                       class="btn btn-sm btn-outline-dark"
                                       title="{{ __('messages.Generate Certificate') }}">
                                        <i class="bi bi-file-earmark-text me-1"></i>{{ __('messages.Certificate') }}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">
                        {{ __('messages.Showing') }} {{ $deceasedMembers->firstItem() }} {{ __('messages.to') }} {{ $deceasedMembers->lastItem() }} {{ __('messages.of') }} {{ $deceasedMembers->total() }} {{ __('messages.entries') }}
                    </small>
                    {{ $deceasedMembers->links('pagination::bootstrap-5') }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-people" style="font-size: 60px; color: #ccc;"></i>
                    <h5 class="mt-3 text-muted">{{ __('messages.No deceased members found') }}</h5>
                    <p class="text-muted">{{ __('messages.Members marked as deceased will appear here.') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
