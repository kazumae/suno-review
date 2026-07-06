<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SongCard from '@/Components/SongCard.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    songs: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="チャート">
        <meta name="description" content="いま注目のSUNO楽曲チャート。閲覧数・レビュースコア・新しさから算出したランキング。" />
    </Head>

    <PublicLayout>
        <div class="mx-auto max-w-6xl px-4 py-10">
            <h1 class="mb-2 flex items-center gap-3 text-2xl font-bold">
                <span class="inline-block h-6 w-1.5 bg-brand-500"></span> SUNO注目曲チャート
            </h1>
            <p class="mb-6 text-sm text-zinc-500">閲覧数 × レビュースコア × 新しさ ＋ 編集部ピックで算出。定期更新。</p>

            <div v-if="songs.length" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                <SongCard v-for="(song, i) in songs" :key="song.id" :song="song" :rank="i + 1" />
            </div>
            <p v-else class="text-zinc-500">チャートはまだありません。</p>
        </div>
    </PublicLayout>
</template>
