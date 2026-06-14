<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: { type: Object, default: () => ({}) },
    recentRequests: { type: Array, default: () => [] },
});

const statusLabel = { open: '未対応', assigned: '対応中', done: '完了', rejected: '却下' };
</script>

<template>
    <Head title="ダッシュボード - Studio" />

    <AdminLayout>
        <h1 class="text-2xl font-bold">ダッシュボード</h1>

        <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="bg-zinc-900 p-5 ring-1 ring-zinc-800">
                <div class="text-3xl font-bold text-brand-500">{{ stats.open_requests ?? 0 }}</div>
                <div class="mt-1 text-sm text-zinc-400">未対応の依頼</div>
            </div>
            <div class="bg-zinc-900 p-5 ring-1 ring-zinc-800">
                <div class="text-3xl font-bold">{{ stats.published_songs ?? 0 }}</div>
                <div class="mt-1 text-sm text-zinc-400">公開中の楽曲</div>
            </div>
            <div class="bg-zinc-900 p-5 ring-1 ring-zinc-800">
                <div class="text-3xl font-bold">{{ stats.songs ?? 0 }}</div>
                <div class="mt-1 text-sm text-zinc-400">全楽曲</div>
            </div>
            <div class="bg-zinc-900 p-5 ring-1 ring-zinc-800">
                <div class="text-3xl font-bold">{{ stats.reviews ?? 0 }}</div>
                <div class="mt-1 text-sm text-zinc-400">レビュー</div>
            </div>
        </div>

        <div class="mt-10">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-lg font-bold">最近のレビュー依頼</h2>
                <Link :href="route('admin.requests.index')" class="text-sm text-brand-400 transition hover:text-brand-300">すべて見る →</Link>
            </div>
            <div class="border border-zinc-800">
                <div v-for="r in recentRequests" :key="r.id" class="flex items-center gap-4 border-b border-zinc-800 p-3 last:border-b-0">
                    <span class="shrink-0 bg-zinc-800 px-2 py-0.5 text-xs text-zinc-300">{{ statusLabel[r.status] ?? r.status }}</span>
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm">{{ r.title || '(無題)' }}</div>
                        <a :href="r.suno_url" target="_blank" rel="noopener" class="truncate text-xs text-zinc-500 transition hover:text-brand-400">{{ r.suno_url }}</a>
                    </div>
                    <span class="shrink-0 text-xs text-zinc-500">{{ r.requester?.name || '匿名' }}</span>
                </div>
                <div v-if="! recentRequests.length" class="p-4 text-sm text-zinc-500">依頼はまだありません。</div>
            </div>
        </div>
    </AdminLayout>
</template>
