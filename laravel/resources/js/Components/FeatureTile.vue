<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    song: { type: Object, required: true },
    large: { type: Boolean, default: false },
});
</script>

<template>
    <Link
        :href="route('songs.show', song.id)"
        class="group relative block h-56 overflow-hidden bg-zinc-900 md:h-full"
    >
        <img
            v-if="song.cover_url"
            :src="song.cover_url"
            :alt="song.title"
            class="absolute inset-0 h-full w-full object-cover opacity-80 transition duration-700 group-hover:scale-105 group-hover:opacity-90"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/30 to-transparent"></div>
        <div class="absolute inset-x-0 bottom-0 p-4">
            <div v-if="song.genre" class="mb-1 inline-block bg-zinc-900/70 px-2 py-0.5 text-xs text-zinc-200">{{ song.genre }}</div>
            <h3 :class="['font-bold leading-tight', large ? 'text-2xl md:text-4xl' : 'text-lg md:text-xl']">{{ song.title }}</h3>
            <p class="mt-1 text-sm text-zinc-300">{{ song.artist_name }}</p>
            <div class="mt-2 flex items-center gap-2 text-xs">
                <span
                    v-if="song.reviews_avg_overall_score"
                    class="border border-brand-500/60 px-1.5 py-0.5 font-semibold text-brand-400"
                >
                    {{ Number(song.reviews_avg_overall_score).toFixed(1) }}
                </span>
                <span v-if="song.reviews_count" class="text-zinc-400">{{ song.reviews_count }}レビュー</span>
            </div>
        </div>
    </Link>
</template>
