@extends('emails.layout')

@section('content')
    <h2>Hello, {{ $user->name }}</h2>

    <p>Your password on {{ config('app.name') }} has been reset by an administrator. Below are your updated login credentials:</p>

    <div class="info-box">
        <strong>Email:</strong> {{ $user->email }}<br>
        <strong>New Password:</strong> {{ $plainPassword }}
    </div>

    <p>For security, please change your password after logging in.</p>

    <p style="text-align: center;">
        <a href="{{ url('/login') }}" class="button">Log In</a>
    </p>

    <p>If you did not expect this change, please contact an administrator immediately.</p>

    <p>Best regards,<br>{{ config('app.name') }} Team</p>
@endsection
