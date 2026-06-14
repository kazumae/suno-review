<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SongCard from '@/Components/SongCard.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    q: { type: String, default: '' },
    songs: { type: Array, default: () => [] },
});
</script>

<template>
    <Head :title="q ? q + ' の検索結果' : '検索'" />

    <PublicLayout>
        <div class="mx-auto max-w-7xl px-4 py-10">
            <h1 class="mb-6 flex items-center gap-3 text-2xl font-bold">
                <span class="inline-block h-6 w-1.5 bg-brand-500"></span>
                <span v-if="q">「{{ q }}」の検索結果</span>
                <span v-else>検索</span>
            </h1>
            <div v-if="songs.length" class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                <SongCard v-for="song in songs" :key="song.id" :song="song" />
            </div>
            <p v-else-if="q" class="text-zinc-500">「{{ q }}」に一致する楽曲は見つかりませんでした。</p>
            <p v-else class="text-zinc-500">キーワードを入力して検索してください。</p>
        </div>
    </PublicLayout>
</template>
