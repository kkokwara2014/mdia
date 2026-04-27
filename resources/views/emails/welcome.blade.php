@extends('emails.layout')

@section('content')
    <h2>Welcome, {{ $user->name }}!</h2>

    <p>Your account has been created on {{ config('app.name') }}. Below are your login credentials:</p>

    <div class="info-box">
        <strong>Email:</strong> {{ $user->email }}<br>
        <strong>Password:</strong> {{ $plainPassword }}
    </div>

    <p>For security, please change your password after your first login.</p>

    <p style="text-align: center;">
        <a href="{{ url('/login') }}" class="button">Log In</a>
    </p>

    <p>If you have any questions, feel free to reach out to us.</p>

    <p>Best regards,<br>{{ config('app.name') }} Team</p>
@endsection
