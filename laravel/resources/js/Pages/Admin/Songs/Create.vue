<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SongForm from '@/Components/SongForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    prefill: { type: Object, default: null },
});
</script>

<template>
    <Head title="楽曲を登録 - Studio" />

    <AdminLayout>
        <h1 class="text-2xl font-bold">楽曲を登録</h1>
        <p v-if="prefill" class="mt-1 text-sm text-zinc-400">レビュー依頼から作成中（依頼は完了に更新されます）</p>
        <div class="mt-6">
            <SongForm :prefill="prefill" submit-label="登録する" />
        </div>
    </AdminLayout>
</template>
