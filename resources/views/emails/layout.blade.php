<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? config('app.name') }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .wrapper {
            background-color: #f3f4f6;
            padding: 32px 16px;
        }

        .container {
            max-width: 560px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            padding: 28px 32px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.3px;
        }

        .header .tagline {
            margin: 6px 0 0;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 400;
        }

        .content {
            padding: 0 32px 32px;
        }

        .status-strip {
            padding: 12px 32px;
            text-align: center;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-completed {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .info-box {
            background: #f9fafb;
            border-radius: 12px;
            padding: 16px;
            margin: 16px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 14px;
        }

        .info-label {
            color: #6b7280;
        }

        .info-value {
            color: #111827;
            font-weight: 500;
            text-align: right;
        }

        .divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 12px 0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 16px;
            font-weight: 700;
        }

        .total-label {
            color: #111827;
        }

        .total-value {
            color: #4f46e5;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4f46e5;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
        }

        .btn-wrap {
            text-align: center;
            margin: 24px 0 8px;
        }

        .footer {
            background: #1f2937;
            padding: 28px 32px;
            text-align: center;
        }

        .footer-desc {
            font-size: 13px;
            color: #d1d5db;
            line-height: 1.6;
            margin: 0 0 20px;
        }

        .footer-divider {
            border: none;
            border-top: 1px solid #374151;
            margin: 0 0 20px;
        }

        .footer-contact {
            margin: 0 0 16px;
        }

        .footer-contact a {
            color: #a5b4fc;
            text-decoration: none;
            font-size: 13px;
        }

        .footer-contact a:hover {
            text-decoration: underline;
        }

        .footer-contact-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin: 6px 0;
            font-size: 13px;
            color: #9ca3af;
        }

        .footer-bottom {
            font-size: 11px;
            color: #6b7280;
            margin: 0;
            line-height: 1.5;
        }

        .footer-bottom a {
            color: #9ca3af;
            text-decoration: none;
        }

        .social-row {
            margin: 0 0 16px;
        }

        .social-row a {
            display: inline-block;
            margin: 0 6px;
            color: #a5b4fc;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>{{ config('app.name') }}</h1>
                <p class="tagline">Top Up Game &amp; Layanan Digital Terpercaya</p>
            </div>
            @hasSection('status')
                <div class="status-strip @yield('status-class')">
                    @yield('status')
                </div>
            @endif
            <div class="content">
                @yield('content')
            </div>
            <div class="footer">
                <p class="footer-desc">{{ config('site.desc') }}</p>

                <hr class="footer-divider">

                <div class="footer-contact">
                    <div class="footer-contact-row">
                        <span>📧</span>
                        <a href="mailto:{{ config('site.cs_email') }}">{{ config('site.cs_email') }}</a>
                    </div>
                    <div class="footer-contact-row">
                        <span>💬</span>
                        <a href="https://wa.me/{{ config('site.cs_wa') }}">WhatsApp {{ config('site.cs_wa') }}</a>
                    </div>
                    <div class="footer-contact-row">
                        <span>🌐</span>
                        <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>
                    </div>
                </div>

                <hr class="footer-divider">

                <p class="footer-bottom">
                    Email ini dikirim secara otomatis oleh <strong>{{ config('app.name') }}</strong>.
                    <br>Jangan balas email ini.
                    <br><br>
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>

</html>
