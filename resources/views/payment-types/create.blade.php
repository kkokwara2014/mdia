@extends('layouts.app')

@section('title', 'Add Payment Type')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">Add Payment Type</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('payment-types.index') }}" class="btn btn-outline-secondary">Back to Payment Types</a>
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

        <form method="POST" action="{{ route('payment-types.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label required">Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label required">Amount</label>
                <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" min="0" step="0.01" placeholder="0.00" required>
                @error('amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Add Payment Type</button>
        </form>
    </div>
</div>
@endsection
