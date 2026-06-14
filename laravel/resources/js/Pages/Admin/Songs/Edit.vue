<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SongForm from '@/Components/SongForm.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    song: { type: Object, required: true },
});
</script>

<template>
    <Head title="楽曲を編集 - Studio" />

    <AdminLayout>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">楽曲を編集</h1>
            <Link :href="route('songs.show', song.id)" class="text-sm text-brand-400 transition hover:text-brand-300">公開ページを見る →</Link>
        </div>
        <div class="mt-6">
            <SongForm :song="song" submit-label="更新する" />
        </div>
    </AdminLayout>
</template>
