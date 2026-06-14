<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    requests: { type: Array, default: () => [] },
});

const statuses = [
    { value: 'open', label: '未対応' },
    { value: 'assigned', label: '対応中' },
    { value: 'done', label: '完了' },
    { value: 'rejected', label: '却下' },
];

const updateStatus = (id, status) => {
    router.patch(route('admin.requests.update', id), { status }, { preserveScroll: true });
};
</script>

<template>
    <Head title="レビュー依頼 - Studio" />

    <AdminLayout>
        <h1 class="text-2xl font-bold">レビュー依頼</h1>

        <div class="mt-6 overflow-x-auto border border-zinc-800">
            <table class="w-full text-sm">
                <thead class="bg-zinc-900 text-left text-zinc-400">
                    <tr>
                        <th class="p-3 font-medium">タイトル / URL</th>
                        <th class="p-3 font-medium">ジャンル</th>
                        <th class="p-3 font-medium">依頼者</th>
                        <th class="p-3 font-medium">ステータス</th>
                        <th class="p-3 font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <tr v-for="r in requests" :key="r.id" class="align-top">
                        <td class="p-3">
                            <div class="font-medium">{{ r.title || '(無題)' }}</div>
                            <a :href="r.suno_url" target="_blank" rel="noopener" class="text-xs text-zinc-500 transition hover:text-brand-400">{{ r.suno_url }}</a>
                            <p v-if="r.note" class="mt-1 max-w-md text-xs text-zinc-500">{{ r.note }}</p>
                        </td>
                        <td class="p-3 text-zinc-400">{{ r.genre || '—' }}</td>
                        <td class="p-3 text-zinc-400">{{ r.requester?.name || '匿名' }}</td>
                        <td class="p-3">
                            <select
                                :value="r.status"
                                class="rounded-none border border-zinc-700 bg-zinc-900 px-2 py-1 text-zinc-100 focus:border-brand-500 focus:outline-none"
                                @change="updateStatus(r.id, $event.target.value)"
                            >
                                <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                            </select>
                        </td>
                        <td class="p-3 whitespace-nowrap">
                            <Link :href="route('admin.songs.create', { request_id: r.id })" class="text-brand-400 transition hover:text-brand-300">曲にする</Link>
                        </td>
                    </tr>
                    <tr v-if="! requests.length">
                        <td colspan="5" class="p-4 text-zinc-500">依頼はまだありません。</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
