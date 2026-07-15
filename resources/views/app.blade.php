<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>{{ config('app.name', 'TopUp Store') }}</title>
    <meta name="description" content="{{ config('site.desc', 'Layanan Web Topup') }}">
    <meta name="keywords" content="{{ config('site.keywoard', 'web,topup') }}">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/images/apple-touch-icon.png">
    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900">
    @inertia
</body>

</html>

</html>
