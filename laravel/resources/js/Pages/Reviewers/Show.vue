<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    reviewer: { type: Object, required: true },
    reviews: { type: Array, default: () => [] },
});
</script>

<template>
    <Head :title="reviewer.name + ' のレビュー'" />

    <PublicLayout>
        <div class="mx-auto max-w-3xl px-4 py-10">
            <!-- Profile -->
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 overflow-hidden bg-zinc-800 ring-1 ring-zinc-800">
                    <img v-if="reviewer.avatar_path" :src="reviewer.avatar_path" class="h-full w-full object-cover" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold">{{ reviewer.name }}</h1>
                    <p class="text-sm text-brand-400">@{{ reviewer.handle }}</p>
                </div>
            </div>
            <p v-if="reviewer.bio" class="mt-4 leading-relaxed text-zinc-300">{{ reviewer.bio }}</p>

            <!-- Reviews -->
            <h2 class="mb-5 mt-10 flex items-center gap-3 text-lg font-bold">
                <span class="inline-block h-5 w-1 bg-brand-500"></span> レビュー {{ reviews.length }}件
            </h2>
            <div class="space-y-4">
                <Link
                    v-for="review in reviews"
                    :key="review.id"
                    :href="route('reviews.show', review.slug)"
                    class="flex items-center gap-4 bg-zinc-900 p-3 ring-1 ring-zinc-800 transition hover:ring-brand-500/60"
                >
                    <div class="h-16 w-28 shrink-0 overflow-hidden bg-zinc-800">
                        <img v-if="review.cover_url" :src="review.cover_url" class="h-full w-full object-cover" />
                    </div>
                    <div class="min-w-0">
                        <div class="truncate font-semibold">{{ review.title }}</div>
                        <div class="truncate text-sm text-zinc-400">{{ review.song?.title }} / {{ review.song?.artist_name }}</div>
                        <div class="mt-1 text-xs text-brand-400">{{ Number(review.overall_score).toFixed(1) }}</div>
                    </div>
                </Link>
                <p v-if="!reviews.length" class="text-zinc-500">まだレビューがありません。</p>
            </div>
        </div>
    </PublicLayout>
</template>
