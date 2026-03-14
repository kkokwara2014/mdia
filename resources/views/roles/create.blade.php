@extends('layouts.app')

@section('title', 'Add Role')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">Add Role</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Back to Roles</a>
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

        <form method="POST" action="{{ route('roles.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label required">Role Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Permissions</label>
                <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                    @foreach($permissions as $permission)
                    <label class="form-selectgroup-item flex-fill">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->uuid }}" class="form-selectgroup-input" {{ in_array($permission->uuid, old('permissions', [])) ? 'checked' : '' }}>
                        <div class="form-selectgroup-label d-flex align-items-center p-3">
                            <div class="me-3">
                                <span class="form-selectgroup-title fw-semibold">{{ $permission->name }}</span>
                                @if($permission->description)
                                    <div class="text-secondary small">{{ $permission->description }}</div>
                                @endif
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('permissions')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Add Role</button>
        </form>
    </div>
</div>
@endsection
