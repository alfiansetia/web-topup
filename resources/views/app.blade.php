<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>{{ config('app.name', 'TopUp Store') }}</title>
    <meta name="description" content="{{ config('site.desc', 'Layanan Web Topup') }}">
    <meta name="keywords" content="{{ config('site.keywoard', 'web,topup') }}">
    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900">
    @inertia
</body>

</html>

</html>
