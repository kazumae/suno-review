<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    song: { type: Object, required: true },
    rank: { type: Number, default: null },
});
</script>

<template>
    <Link :href="route('songs.show', song.id)" class="group block">
        <div class="relative overflow-hidden bg-zinc-900 ring-1 ring-zinc-800 transition group-hover:ring-brand-500/60">
            <div class="aspect-video w-full overflow-hidden bg-zinc-800">
                <img
                    v-if="song.cover_url"
                    :src="song.cover_url"
                    :alt="song.title"
                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                    loading="lazy"
                />
            </div>
            <div
                v-if="rank"
                class="absolute left-0 top-0 flex h-7 w-7 items-center justify-center bg-brand-500 text-sm font-bold text-black"
            >
                {{ rank }}
            </div>
            <div class="p-3">
                <h3 class="truncate font-semibold text-zinc-100 transition group-hover:text-brand-400">{{ song.title }}</h3>
                <p class="truncate text-sm text-zinc-400">{{ song.artist_name }}</p>
                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-zinc-500">
                    <span v-if="song.genre" class="bg-zinc-800 px-2 py-0.5 text-zinc-300">{{ song.genre }}</span>
                    <span
                        v-if="song.reviews_avg_overall_score"
                        class="border border-brand-500/50 px-1.5 py-0.5 font-semibold text-brand-400"
                    >
                        {{ Number(song.reviews_avg_overall_score).toFixed(1) }}
                    </span>
                    <span v-if="song.reviews_count">{{ song.reviews_count }}レビュー</span>
                </div>
            </div>
        </div>
    </Link>
</template>
