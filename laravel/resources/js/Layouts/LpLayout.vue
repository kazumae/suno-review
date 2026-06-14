<script setup>
// LP専用レイアウト: サイト内の他ページへ遷移させない自己完結ファネル。
// ロゴは非リンク、ナビ/検索なし、CTAはページ内フォーム(#request)へのスクロールのみ。
</script>

<template>
    <div id="top" class="flex min-h-screen flex-col bg-zinc-950 text-zinc-100 antialiased">
        <header class="sticky top-0 z-40 border-b border-zinc-800 bg-zinc-950/80 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center px-4">
                <span class="text-lg font-bold tracking-tight">SUNO <span class="text-brand-500">Review</span></span>
                <a
                    href="#request"
                    class="ml-auto rounded-full bg-brand-500 px-4 py-2 text-sm font-semibold text-black transition hover:bg-brand-400"
                >
                    レビュー依頼
                </a>
            </div>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="mt-16 border-t border-zinc-800">
            <div class="mx-auto max-w-7xl px-4 py-10 text-sm text-zinc-500">
                <div class="font-bold text-zinc-300">SUNO <span class="text-brand-500">Review</span></div>
                <p class="mt-1">SUNOの楽曲に、ストーリーを。レビューで次のヒットを。</p>
                <p class="mt-6 text-xs text-zinc-600">© 2026 SUNO Review</p>
            </div>
        </footer>
    </div>
</template>
