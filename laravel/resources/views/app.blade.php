<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead

        {{-- デフォルトOGP / Twitterカード（個別ページはInertiaのHeadで上書き。フォールバックとして@inertiaHead後に配置） --}}
        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="SUNO Review｜SUNOの楽曲に、ストーリーを。">
        <meta property="og:description" content="SUNOで作られた楽曲をレビュワーが聴き込み、ストーリーを添える音楽レビューマガジン。次のヒットをここから。">
        <meta property="og:image" content="{{ url('/images/og-default.png') }}">
        <meta property="og:image:width" content="1896">
        <meta property="og:image:height" content="830">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="SUNO Review｜SUNOの楽曲に、ストーリーを。">
        <meta name="twitter:description" content="SUNOで作られた楽曲をレビュワーが聴き込み、ストーリーを添える音楽レビューマガジン。次のヒットをここから。">
        <meta name="twitter:image" content="{{ url('/images/og-default.png') }}">
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
