@extends('layouts.app')

@section('title', 'Edit Member')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">Edit {{ $member->name }}</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('members.show', $member) }}" class="btn btn-outline-secondary">Back to Profile</a>
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

        <form method="POST" action="{{ route('members.update', $member) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label required">Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $member->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label required">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $member->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label required">Phone</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $member->phone) }}" required>
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">User Image (optional)</label>
                @if($member->user_image)
                    <div class="mb-2">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($member->user_image) }}" alt="{{ $member->name }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%;">
                    </div>
                @endif
                <input type="file" name="user_image" class="form-control @error('user_image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp">
                <small class="form-hint">Leave empty to keep current image</small>
                @error('user_image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Roles</label>
                <div class="row">
                    @foreach($roles as $role)
                    <div class="col-md-4">
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->uuid }}" {{ $member->roles->contains('uuid', $role->uuid) ? 'checked' : '' }}>
                            <span class="form-check-label">{{ $role->name }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>
                <small class="text-muted">Select all roles that apply to this member.</small>
            </div>
            <button type="submit" class="btn btn-primary">Update Member</button>
        </form>
    </div>
</div>
@endsection
