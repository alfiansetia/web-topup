<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? config('app.name') }}</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .wrapper { background-color: #f3f4f6; padding: 32px 16px; }
        .container { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .header { padding: 24px 32px; text-align: center; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 700; color: #111827; }
        .content { padding: 0 32px 32px; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; margin-bottom: 16px; }
        .badge-yellow { background-color: #fef3c7; color: #92400e; }
        .badge-green { background-color: #d1fae5; color: #065f46; }
        .badge-blue { background-color: #dbeafe; color: #1e40af; }
        .info-box { background: #f9fafb; border-radius: 12px; padding: 16px; margin: 16px 0; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; }
        .info-label { color: #6b7280; }
        .info-value { color: #111827; font-weight: 500; text-align: right; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 12px 0; }
        .total-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 16px; font-weight: 700; }
        .total-label { color: #111827; }
        .total-value { color: #4f46e5; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #4f46e5; color: #ffffff !important; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 600; text-align: center; }
        .btn-wrap { text-align: center; margin: 24px 0 8px; }
        .footer { padding: 20px 32px; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { margin: 0; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>{{ config('app.name') }}</h1>
            </div>
            <div class="content">
                @yield('content')
            </div>
            <div class="footer">
                <p>Email ini dikirim secara otomatis oleh {{ config('app.name') }}.</p>
                <p style="margin-top: 4px;">Jangan balas email ini.</p>
            </div>
        </div>
    </div>
</body>
</html>
