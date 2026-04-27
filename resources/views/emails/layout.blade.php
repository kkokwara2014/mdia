<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Space Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f4f7;
            color: #333333;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f4f4f7;
            padding: 30px 0;
        }

        .email-content {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background-color: #ffffff;
            padding: 28px 24px;
            text-align: center;
            border-bottom: 1px solid #eeeeee;
        }

        .email-header h1 {
            color: #13400C;
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }

        .email-body {
            padding: 32px 24px;
            line-height: 1.6;
            font-size: 15px;
        }

        .email-body h2 {
            font-size: 20px;
            margin-top: 0;
            margin-bottom: 16px;
            color: #1a1a1a;
        }

        .email-body p {
            margin: 0 0 16px;
        }

        .email-body .button {
            display: inline-block;
            background-color: #8C160B;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            margin: 8px 0;
        }

        .email-body .info-box {
            background-color: #f9f9f9;
            border-left: 4px solid #8C160B;
            padding: 12px 16px;
            margin: 16px 0;
            border-radius: 0 4px 4px 0;
        }

        .email-footer {
            padding: 20px 24px;
            text-align: center;
            font-size: 12px;
            color: #888888;
            border-top: 1px solid #eeeeee;
        }

        .email-footer a {
            color: #13400C;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-content">
            <div class="email-header">
                <img src="{{ asset('assets/transparent_circulr_logo.png') }}" alt="{{ config('app.name') }}" width="80" height="80" style="display: block; margin: 0 auto 12px;">
                <h1>{{ config('app.name') }}</h1>
            </div>

            <div class="email-body">
                @yield('content')
            </div>

            <div class="email-footer">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                @hasSection('footer')
                    @yield('footer')
                @endif
            </div>
        </div>
    </div>
</body>
</html>
