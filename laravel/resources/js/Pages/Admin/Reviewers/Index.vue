<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    reviewers: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="レビュワー - Studio" />

    <AdminLayout>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">レビュワー</h1>
            <Link :href="route('admin.reviewers.create')" class="rounded-full bg-brand-500 px-4 py-2 text-sm font-semibold text-black transition hover:bg-brand-400">
                ＋ レビュワーを招待
            </Link>
        </div>

        <div class="mt-6 overflow-x-auto border border-zinc-800">
            <table class="w-full text-sm">
                <thead class="bg-zinc-900 text-left text-zinc-400">
                    <tr>
                        <th class="p-3 font-medium">名前</th>
                        <th class="p-3 font-medium">ハンドル</th>
                        <th class="p-3 font-medium">メール</th>
                        <th class="p-3 font-medium">権限</th>
                        <th class="p-3 font-medium">レビュー数</th>
                        <th class="p-3 font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <tr v-for="r in reviewers" :key="r.id">
                        <td class="p-3 font-medium">{{ r.name }}</td>
                        <td class="p-3 text-zinc-400">
                            <a v-if="r.handle" :href="route('reviewers.show', r.handle)" target="_blank" class="hover:text-brand-400">@{{ r.handle }}</a>
                            <span v-else>—</span>
                        </td>
                        <td class="p-3 text-zinc-400">{{ r.email }}</td>
                        <td class="p-3 text-zinc-400">{{ r.role === 'admin' ? '管理者' : 'レビュワー' }}</td>
                        <td class="p-3 text-zinc-400">{{ r.reviews_count }}</td>
                        <td class="p-3">
                            <Link :href="route('admin.reviewers.edit', r.id)" class="text-xs text-zinc-400 hover:text-brand-400">編集</Link>
                        </td>
                    </tr>
                    <tr v-if="! reviewers.length">
                        <td colspan="6" class="p-4 text-zinc-500">レビュワーがいません。</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
