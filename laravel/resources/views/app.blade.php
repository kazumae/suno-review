<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Google Analytics 4 (gtag.js)。GA4_MEASUREMENT_ID 設定時のみ、head のなるべく上に設置 --}}
        @if (config('services.ga4.measurement_id'))
            <!-- Google tag (gtag.js) -->
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.ga4.measurement_id') }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '{{ config('services.ga4.measurement_id') }}');
            </script>
            <!-- End Google tag -->
        @endif

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead

        {{-- 共通OGP（全ページ）。site_name / type / url は常に出す --}}
        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">

        {{-- デフォルトのタイトル / 説明 / 画像。
             Songs/Show・Reviews/Show など自前の og を持つページでこれを出すと、
             og:image / og:title / twitter:image が重複し、X(Twitter) が誤った方を採用して
             カード画像が表示されなくなる。そのため該当ページではスキップする。
             （og を自前で持つページを追加したら、下の配列にも追加すること） --}}
        @unless (in_array($page['component'] ?? null, ['Songs/Show', 'Reviews/Show'], true))
            <meta property="og:title" content="SUNO Review｜SUNOの楽曲に、ストーリーを。">
            <meta property="og:description" content="SUNOで作られた楽曲をレビュワーが聴き込み、ストーリーを添える音楽レビューマガジン。次のヒットをここから。">
            <meta property="og:image" content="{{ url('/images/og-default.png') }}">
            <meta property="og:image:width" content="1896">
            <meta property="og:image:height" content="830">
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:title" content="SUNO Review｜SUNOの楽曲に、ストーリーを。">
            <meta name="twitter:description" content="SUNOで作られた楽曲をレビュワーが聴き込み、ストーリーを添える音楽レビューマガジン。次のヒットをここから。">
            <meta name="twitter:image" content="{{ url('/images/og-default.png') }}">
        @endunless
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
