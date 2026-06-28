<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Google Tag Manager（GTM_CONTAINER_ID 設定時のみ）。head 内のなるべく上に1箇所だけ設置 --}}
        @if (config('services.gtm.id'))
            <!-- Google Tag Manager -->
            <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','{{ config('services.gtm.id') }}');</script>
            <!-- End Google Tag Manager -->
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
        {{-- Google Tag Manager (noscript)。body 開始直後に設置 --}}
        @if (config('services.gtm.id'))
            <!-- Google Tag Manager (noscript) -->
            <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ config('services.gtm.id') }}"
                height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
            <!-- End Google Tag Manager (noscript) -->
        @endif
        @inertia
    </body>
</html>
