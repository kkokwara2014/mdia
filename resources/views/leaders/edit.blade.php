@extends('layouts.app')

@section('title', 'Edit Leader')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="page-header-actions">
        <h2 class="page-title mb-3 mb-md-0">Edit Leader</h2>
        <div class="page-header-buttons d-flex flex-column flex-md-row flex-md-wrap gap-2 justify-content-md-end">
            <a href="{{ route('leaders.index') }}" class="btn btn-outline-secondary order-first">Back to Leaders</a>
            @if($leader->is_published)
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#leaderEditUnpublishModal">
                Unpublish
            </button>
            @else
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#leaderEditPublishModal">
                Publish
            </button>
            @endif
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <div class="d-flex">
                    <div>
                        <h4 class="alert-title">Please fix the following errors</h4>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('leaders.update', $leader) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="d-flex align-items-center gap-3 mb-3">
                <img src="{{ $leader->getImageUrl() }}" alt="" style="width:60px; height:60px; object-fit:cover; border-radius:50%;">
                <div>
                    <strong>{{ $leader->getName() }}</strong>
                    <div class="text-muted" style="font-size:12px;">Linked member (cannot be changed)</div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Member photo</label>
                <input type="file" name="user_image" class="form-control @error('user_image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                <small class="text-muted">JPG, PNG, or WEBP, max 2&nbsp;MB. Leave empty to keep the current photo.</small>
                @error('user_image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Position <span class="text-danger">*</span></label>
                <input type="text" name="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position', $leader->position) }}" required>
                @error('position')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Display Order</label>
                <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $leader->order) }}" min="0" step="1">
                <small class="text-muted">Lower number appears first</small>
                @error('order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @include('leaders.partials.social-links', ['initialSocialLinks' => $leader->social_links ?? []])
            @error('social_links')
                <div class="text-danger small mb-2">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn btn-primary">Update Leader</button>
        </form>
    </div>
</div>

@if($leader->is_published)
<div class="modal modal-blur fade" id="leaderEditUnpublishModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Unpublish leader</div>
                <div>Remove <strong>{{ $leader->getName() }}</strong> from the public about page?</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('leaders.unpublish', $leader) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">Unpublish</button>
                </form>
            </div>
        </div>
    </div>
</div>
@else
<div class="modal modal-blur fade" id="leaderEditPublishModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Publish leader</div>
                <div>Publish <strong>{{ $leader->getName() }}</strong> on the public about page?</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('leaders.publish', $leader) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">Publish</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
