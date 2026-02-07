<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php($faviconVersion = @filemtime(public_path('favicon.ico')) ?: time())
    <link rel="icon" type="image/png" href="{{ asset('favicon-96x96.png') }}?v={{ $faviconVersion }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v={{ $faviconVersion }}" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ $faviconVersion }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v={{ $faviconVersion }}" />
    <link rel="manifest" href="{{ asset('site.webmanifest') }}?v={{ $faviconVersion }}" />

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
    @yield('body')
</body>

</html>