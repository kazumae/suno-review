<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SongCard from '@/Components/SongCard.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    genre: { type: String, required: true },
    songs: { type: Array, default: () => [] },
});
</script>

<template>
    <Head :title="genre + ' の楽曲'" />

    <PublicLayout>
        <div class="mx-auto max-w-6xl px-4 py-10">
            <h1 class="mb-6 flex items-center gap-3 text-2xl font-bold">
                <span class="inline-block h-6 w-1.5 bg-brand-500"></span> {{ genre }}
            </h1>
            <div v-if="songs.length" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                <SongCard v-for="song in songs" :key="song.id" :song="song" />
            </div>
            <p v-else class="text-zinc-500">このジャンルの楽曲はまだありません。</p>
        </div>
    </PublicLayout>
</template>
