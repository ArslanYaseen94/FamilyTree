@extends('layouts.user.app')
@section('content')
<div class="main-content right-chat-active">
    <div class="middle-sidebar-bottom">
        <div class="middle-sidebar-left pe-0">
            <div class="container">
                <div class="card-header bg-primary text-white rounded-4">
                    <h4 class="mb-0 p-4"> {{ __('messages.Create New Blog') }}</h4>
                </div>
                <div class="card shadow rounded-4 mt-3">
                    <div class="card-body mt-3">
                        <form action="{{ route('blogs.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="blog_id" value="{{$blogs->id ?? ""}}">
                            <div class="mb-3">
                                <label class="form-label"> {{ __('messages.Title') }}</label>
                                <input type="text" value="{{ $blogs->title ?? '' }}" name="title"
                                    class="form-control" required placeholder="{{ __('messages.Enter Blog Title') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label"> {{ __('messages.Slug') }}</label>
                                <input type="text" name="slug" class="form-control"
                                    value="{{ $blogs->slug ?? '' }}" placeholder="{{ __('messages.Enter Slug') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label"> {{ __('messages.Excerpt') }}</label>
                                <textarea name="excerpt" class="form-control" rows="2" placeholder="{{ __('messages.Excerpt') }}">{{ $blogs->excerpt ?? '' }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"> {{ __('messages.Content') }}</label>
                                <textarea name="content" class="form-control" rows="5"  required placeholder="{{ __('messages.Content') }}">{{ $blogs->content ?? '' }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"> {{ __('messages.Featured Image') }}</label>
                                <input type="file" name="featured_image" id="featuredImage" class="form-control" accept="image/jpeg,image/jpg,image/png,image/gif,image/bmp,image/webp,image/svg+xml">
                                <small class="text-muted">{{ __('messages.Only image files allowed: jpeg, jpg, png, gif, bmp, webp, svg. Max 5MB.') }}</small>
                                @if (isset($blogs) && $blogs->featured_image)
                                <img width="100" height="100" src="{{ asset($blogs->featured_image) }}" alt=""
                                    class="mt-2 image-fluid">
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label"> {{ __('messages.Status') }}</label>
                                <select name="status" class="form-select" required>
                                    @php
                                    $validStatuses = ['draft', 'published', 'archived'];
                                    $selectedStatus = in_array($blogs->status ?? '', $validStatuses)
                                    ? $blogs->status
                                    : '';
                                    @endphp
                                    <option value="draft" {{ $selectedStatus === 'draft' ? 'selected' : '' }}>{{ __('messages.Draft') }}
                                    </option>
                                    <option value="published" {{ $selectedStatus === 'published' ? 'selected' : '' }}>
                                        {{ __('messages.Published') }}</option>
                                    <option value="archived" {{ $selectedStatus === 'archived' ? 'selected' : '' }}>
                                        {{ __('messages.Archived') }}</option>
                                </select>
                            </div>


                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.Published At') }}</label>
                                <input type="datetime-local" value="{{$blogs->published_at ?? ""}}" name="published_at" class="form-control" placeholder="{{ __('messages.Published At') }}">
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-success px-4"> {{ __('messages.Publish') }}</button>
                                <a href="{{ url()->previous() }}" class="btn btn-secondary px-4"> {{ __('messages.Cancel') }}</a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var imageInput = document.getElementById('featuredImage');
    var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'image/svg+xml'];
    var maxSize = 5 * 1024 * 1024;

    imageInput.addEventListener('change', function() {
        var file = this.files[0];
        if (!file) return;

        if (!allowedTypes.includes(file.type)) {
            toastr.error("{{ __('messages.Only image files are allowed (jpeg, jpg, png, gif, bmp, webp, svg).') }}");
            this.value = '';
            return;
        }

        if (file.size > maxSize) {
            toastr.error("{{ __('messages.Each image must be less than 5MB.') }}");
            this.value = '';
            return;
        }
    });
});
</script>
@endsection