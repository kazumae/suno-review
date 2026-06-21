<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    reviewers: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="レビュワー" />

    <PublicLayout>
        <div class="mx-auto max-w-6xl px-4 py-10">
            <header class="mb-8">
                <h1 class="flex items-center gap-3 text-2xl font-bold">
                    <span class="inline-block h-6 w-1 bg-brand-500"></span> レビュワー
                </h1>
                <p class="mt-2 text-sm text-zinc-400">SUNOの楽曲を聴き込み、レビューを綴る書き手たち。</p>
            </header>

            <div v-if="reviewers.length" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="r in reviewers"
                    :key="r.handle"
                    :href="route('reviewers.show', r.handle)"
                    class="group flex gap-4 bg-zinc-900 p-4 ring-1 ring-zinc-800 transition hover:ring-brand-500/60"
                >
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden bg-zinc-800 text-lg font-bold text-zinc-500 ring-1 ring-zinc-800">
                        <img v-if="r.avatar_url" :src="r.avatar_url" :alt="r.name" class="h-full w-full object-cover" loading="lazy" />
                        <span v-else>{{ r.name?.charAt(0) }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="truncate font-bold transition group-hover:text-brand-400">{{ r.name }}</div>
                        <div class="truncate text-xs text-brand-400">@{{ r.handle }}</div>
                        <p v-if="r.bio" class="mt-1 line-clamp-2 text-sm text-zinc-400">{{ r.bio }}</p>
                        <div class="mt-2 text-xs text-zinc-500">レビュー {{ r.reviews_count }}件</div>
                    </div>
                </Link>
            </div>
            <p v-else class="text-zinc-500">まだレビュワーがいません。</p>
        </div>
    </PublicLayout>
</template>
