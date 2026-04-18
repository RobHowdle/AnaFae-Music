<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
    $seoTitle = $title ?? 'Ana Fae Music';
    $seoDescription = $description ?? 'Live wedding and event music from Ana Fae Music, with bespoke ceremony songs, drinks reception sets, evening entertainment, and curated playlists.';
    $seoCanonical = $canonical ?? url()->current();
    $seoImage = $image ?? asset('images/logo.png');
    $seoImageAlt = $imageAlt ?? 'Ana Fae Music logo';
    $seoType = $type ?? 'website';
    $seoRobots = $robots ?? 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1';
    $seoSiteName = config('app.name', 'Ana Fae Music');
    $schema = [
    '@context' => 'https://schema.org',
    '@type' => 'PerformingGroup',
    'name' => $seoSiteName,
    'url' => rtrim(config('app.url'), '/'),
    'description' => $seoDescription,
    'image' => $seoImage,
    'logo' => $seoImage,
    'sameAs' => [
    'https://www.lastminutemusicians.com/members/anafae.html',
    'https://www.northeastweddingnetwork.co.uk/ana-fae-music',
    ],
    ];
    @endphp

    @php($faviconVersion = @filemtime(public_path('favicon.ico')) ?: time())
    <link rel="icon" type="image/png" href="{{ asset('favicon-96x96.png') }}?v={{ $faviconVersion }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v={{ $faviconVersion }}" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ $faviconVersion }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v={{ $faviconVersion }}" />
    <link rel="manifest" href="{{ asset('site.webmanifest') }}?v={{ $faviconVersion }}" />
    <link rel="canonical" href="{{ $seoCanonical }}" />

    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}" />
    <meta name="robots" content="{{ $seoRobots }}" />
    <meta name="theme-color" content="#f6eef8" />

    <meta property="og:locale" content="en_GB" />
    <meta property="og:type" content="{{ $seoType }}" />
    <meta property="og:site_name" content="{{ $seoSiteName }}" />
    <meta property="og:title" content="{{ $seoTitle }}" />
    <meta property="og:description" content="{{ $seoDescription }}" />
    <meta property="og:url" content="{{ $seoCanonical }}" />
    <meta property="og:image" content="{{ $seoImage }}" />
    <meta property="og:image:alt" content="{{ $seoImageAlt }}" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $seoTitle }}" />
    <meta name="twitter:description" content="{{ $seoDescription }}" />
    <meta name="twitter:image" content="{{ $seoImage }}" />
    <meta name="twitter:image:alt" content="{{ $seoImageAlt }}" />

    <script type="application/ld+json">
        {
            !!json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
    @yield('body')
</body>

</html>