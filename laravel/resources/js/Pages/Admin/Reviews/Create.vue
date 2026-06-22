<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ReviewForm from '@/Components/ReviewForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    selectedSong: { type: Object, default: null },
    songId: { type: [Number, String, null], default: null },
});
</script>

<template>
    <Head title="レビューを書く - Studio" />

    <AdminLayout>
        <h1 class="text-2xl font-bold">レビューを書く</h1>
        <div class="mt-6">
            <ReviewForm :selected-song="selectedSong" :song-id="songId" submit-label="公開する" />
        </div>
    </AdminLayout>
</template>
