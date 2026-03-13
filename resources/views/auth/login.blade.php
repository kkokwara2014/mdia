@extends('adminlte::auth.login')

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/mdia-theme.css') }}">
    @stack('css')
    @yield('css')
@stop

@section('auth_header', 'Sign in to start your session')

@section('auth_body')
    <form action="{{ route('login.submit') }}" method="post">
        @csrf

        <div class="input-group mb-3">
            <input type="email" 
                   name="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   placeholder="Email"
                   value="{{ old('email') }}"
                   required
                   autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
            @error('email')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="input-group mb-3">
            <input type="password" 
                   name="password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   placeholder="Password"
                   required>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
            @error('password')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="row">
            <div class="col-7">
                <div class="icheck-primary">
                    <input type="checkbox" id="remember" name="remember" value="1">
                    <label for="remember">
                        Remember Me
                    </label>
                </div>
            </div>
            <div class="col-5">
                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </div>
        </div>
    </form>
@stop

@section('auth_footer')
@stop

@push('css')
<style>
    .login-logo img {
        max-width: 60px !important;
        max-height: 60px !important;
        width: 60px !important;
        height: 60px !important;
        object-fit: contain;
        margin-bottom: 10px;
    }
    .login-logo a {
        color: transparent;
        font-size: 0;
    }
</style>
@endpush
