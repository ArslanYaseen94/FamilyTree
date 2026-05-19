@extends('layouts.user.app')

@section('content')
<style>
    .media-card {
        width: 100%;
        aspect-ratio: 1 / 1;
        object-fit: cover;
        border-radius: 0.5rem;
    }

    .media-wrapper {
        height: 250px;
        overflow: hidden;
        position: relative;
    }

    .delete-photo-btn {
        z-index: 10;
    }
</style>

<div class="main-content right-chat-active">
    <div class="middle-sidebar-bottom">
        <div class="middle-sidebar-left pe-0">
            <div class="container mt-4">
                <h4>{{ __('messages.Upload a Photo') }}</h4>
                <form action="{{ route('user.upload.photo') }}" method="POST" enctype="multipart/form-data" id="photoUploadForm">
                    @csrf
                    <div class="mb-3">
                        <input type="file" name="media[]" id="mediaInput" class="form-control" required multiple accept="image/jpeg,image/jpg,image/png,image/gif,image/bmp,image/webp,image/svg+xml">
                        <small class="text-muted">{{ __('messages.Only photo files allowed: jpeg, jpg, png, gif, bmp, webp, svg. Max 5MB each.') }}</small>
                    </div>
                    <button type="submit" class="btn btn-primary" id="uploadBtn">{{ __('messages.Upload Photos') }}</button>
                </form>
            </div>

            <div class="container mt-4">
                <h4>{{ __('messages.Your Uploaded Photos') }}</h4>

                @if (count($photos))
                    <div class="row">
                        @foreach ($photos as $photo)
                            @php
                                $extension = strtolower(pathinfo($photo, PATHINFO_EXTENSION));
                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg']);
                            @endphp
                            @if ($isImage)
                            <div class="col-md-3 mb-4">
                                <div class="card media-wrapper position-relative">
                                    <img src="{{ $photo }}" class="media-card" alt="User Photo">
                                    <button
                                        class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 delete-photo-btn"
                                        data-photo="{{ $photo }}" title="Delete">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p>{{ __('messages.No photos uploaded yet.') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'image/svg+xml'];
        var maxSize = 5 * 1024 * 1024; // 5MB

        document.getElementById('photoUploadForm').addEventListener('submit', function(e) {
            var files = document.getElementById('mediaInput').files;

            if (files.length === 0) {
                e.preventDefault();
                toastr.error('{{ __("messages.Please select at least one photo.") }}');
                return;
            }

            for (var i = 0; i < files.length; i++) {
                if (!allowedTypes.includes(files[i].type)) {
                    e.preventDefault();
                    toastr.error('{{ __("messages.Only image files are allowed. No videos or audio files.") }}');
                    return;
                }
                if (files[i].size > maxSize) {
                    e.preventDefault();
                    toastr.error(files[i].name + ' {{ __("messages.exceeds the 5MB size limit.") }}');
                    return;
                }
            }

            document.getElementById('uploadBtn').disabled = true;
            document.getElementById('uploadBtn').innerHTML = '<span class="spinner-border spinner-border-sm"></span> {{ __("messages.Uploading...") }}';
        });

        document.querySelectorAll('.delete-photo-btn').forEach(button => {
            button.addEventListener('click', function () {
                const photoUrl = this.getAttribute('data-photo');

                Swal.fire({
                    title: '{{ __("messages.Are you sure?") }}',
                    text: '{{ __("messages.This photo will be permanently deleted.") }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '{{ __("messages.Yes, delete it!") }}',
                    cancelButtonText: '{{ __("messages.Cancel") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch("{{ route('user.photos.delete') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ photo: photoUrl })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                toastr.success(data.message);
                                setTimeout(() => location.reload(), 1000);
                            } else {
                                toastr.error(data.message);
                            }
                        })
                        .catch(() => {
                            toastr.error('{{ __("messages.Something went wrong.") }}');
                        });
                    }
                });
            });
        });
    });
</script>
@endsection
