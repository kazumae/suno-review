<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ReviewForm from '@/Components/ReviewForm.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    review: { type: Object, required: true },
    selectedSong: { type: Object, default: null },
});
</script>

<template>
    <Head title="レビューを編集 - Studio" />

    <AdminLayout>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">レビューを編集</h1>
            <Link :href="route('reviews.show', review.slug)" class="text-sm text-brand-400 transition hover:text-brand-300">公開ページを見る →</Link>
        </div>
        <div class="mt-6">
            <ReviewForm :review="review" :selected-song="selectedSong" submit-label="更新する" />
        </div>
    </AdminLayout>
</template>
