@extends('layouts.app')

@section('title', 'Add Member')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">Add Member</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">Back to Members</a>
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

        <form method="POST" action="{{ route('members.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label required">Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label required">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label required">Phone</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">User Image (optional)</label>
                <input type="file" name="user_image" class="form-control @error('user_image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp">
                <small class="form-hint">Max file size: 2MB. Formats: JPG, PNG, WEBP</small>
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
                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->uuid }}" {{ in_array($role->uuid, old('roles', [])) ? 'checked' : '' }}>
                            <span class="form-check-label">{{ $role->name }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>
                <small class="text-muted">If no role is selected, Member role will be assigned by default.</small>
            </div>
            <button type="submit" class="btn btn-primary">Add Member</button>
        </form>
    </div>
</div>
@endsection
