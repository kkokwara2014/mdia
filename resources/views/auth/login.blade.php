@extends('layouts.auth')

@section('title', 'Sign in')

@section('content')
<div class="container-tight py-4">
    <div class="text-center mb-4">
        <img src="{{ asset('assets/transparent_circulr_logo.png') }}" alt="" class="rounded" style="max-width: clamp(80px, 20vw, 120px); height: auto; object-fit: contain;">
        <h2 class="mt-2">{{ config('app.name') }}</h2>
    </div>
    <div class="card card-md">
        <div class="card-body">
            <h2 class="card-title text-center mb-4">Sign in to your account</h2>
            @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0 list-unstyled">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <form action="{{ route('login.submit') }}" method="post" autocomplete="off">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" name="remember" class="form-check-input" value="1">
                        <span class="form-check-label">Remember me</span>
                    </label>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Sign in</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
