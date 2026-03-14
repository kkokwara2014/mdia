@extends('layouts.app')

@section('title', 'Edit Permission')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">Edit {{ $permission->name }}</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary">Back to Permissions</a>
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

        <form method="POST" action="{{ route('permissions.update', $permission) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label required">Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $permission->name) }}" placeholder="validate_payment" required>
                <small class="form-hint">Use snake_case e.g. validate_payment</small>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $permission->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Update Permission</button>
        </form>
    </div>
</div>
@endsection
