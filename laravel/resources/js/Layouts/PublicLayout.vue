<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const flash = computed(() => page.props.flash?.success);

const q = ref(page.props.q ?? '');
const search = () => {
    if (q.value.trim() === '') {
        return;
    }
    router.get(route('search'), { q: q.value }, { preserveState: false });
};
</script>

<template>
    <div class="flex min-h-screen flex-col bg-zinc-950 text-zinc-100 antialiased">
        <!-- Header -->
        <header class="sticky top-0 z-40 border-b border-zinc-800 bg-zinc-950/80 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center gap-4 px-4">
                <Link :href="route('home')" class="shrink-0 text-lg font-bold tracking-tight">
                    SUNO <span class="text-brand-500">Review</span>
                </Link>
                <nav class="hidden items-center gap-5 text-sm text-zinc-400 md:flex">
                    <Link :href="route('home')" class="transition hover:text-white">ホーム</Link>
                    <a href="/#genres" class="transition hover:text-white">ジャンル</a>
                    <Link :href="route('reviewers.index')" class="transition hover:text-white">レビュワー</Link>
                </nav>
                <form class="ml-auto" @submit.prevent="search">
                    <input
                        v-model="q"
                        type="search"
                        placeholder="楽曲を検索"
                        class="w-32 rounded-full border border-zinc-700 bg-zinc-900 px-4 py-1.5 text-sm text-zinc-100 placeholder-zinc-500 transition focus:border-brand-500 focus:outline-none sm:w-44 lg:w-56"
                    />
                </form>
                <div class="flex shrink-0 items-center gap-3">
                    <Link
                        :href="route('requests.create')"
                        class="rounded-full bg-brand-500 px-4 py-2 text-sm font-semibold text-black transition hover:bg-brand-400"
                    >
                        レビュー依頼
                    </Link>
                    <Link
                        v-if="user"
                        :href="route('admin.dashboard')"
                        class="hidden text-sm text-zinc-300 transition hover:text-white sm:block"
                    >
                        管理
                    </Link>
                </div>
            </div>
        </header>

        <!-- Flash -->
        <div v-if="flash" class="border-b border-brand-500/30 bg-brand-500/10">
            <div class="mx-auto max-w-7xl px-4 py-3 text-sm text-brand-300">{{ flash }}</div>
        </div>

        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="mt-16 border-t border-zinc-800">
            <div class="mx-auto max-w-7xl px-4 py-10 text-sm text-zinc-500">
                <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                    <div>
                        <div class="font-bold text-zinc-300">SUNO <span class="text-brand-500">Review</span></div>
                        <p class="mt-1">SUNOの楽曲に、ストーリーを。レビューで次のヒットを。</p>
                    </div>
                    <nav class="flex gap-5">
                        <Link :href="route('reviewers.index')" class="transition hover:text-zinc-300">レビュワー</Link>
                        <Link :href="route('requests.create')" class="transition hover:text-zinc-300">レビュー依頼</Link>
                    </nav>
                </div>
                <p class="mt-6 text-xs text-zinc-600">© 2026 SUNO Review</p>
            </div>
        </footer>
    </div>
</template>
