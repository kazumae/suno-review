<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    applications: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const statuses = [
    { value: 'pending', label: '保留中' },
    { value: 'approved', label: '承認済み' },
    { value: 'rejected', label: '却下' },
];

const status = ref(props.filters.status || '');
const q = ref(props.filters.q || '');

const applyFilters = () => {
    router.get(
        route('admin.applications.index'),
        { status: status.value || undefined, q: q.value || undefined },
        { preserveState: true, preserveScroll: true, replace: true },
    );
};

const clearFilters = () => {
    status.value = '';
    q.value = '';
    applyFilters();
};

const updateStatus = (id, value) => {
    router.patch(route('admin.applications.update', id), { status: value }, { preserveScroll: true });
};

const formatDate = (iso) => (iso ? new Date(iso).toLocaleDateString('ja-JP') : '');
</script>

<template>
    <Head title="レビュワー申込み - Studio" />

    <AdminLayout>
        <h1 class="text-2xl font-bold">レビュワー申込み</h1>

        <!-- 絞り込み -->
        <div class="mt-6 flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-zinc-400">ステータス</label>
                <select
                    v-model="status"
                    class="mt-1 rounded-none border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm text-zinc-100 focus:border-brand-500 focus:outline-none"
                    @change="applyFilters"
                >
                    <option value="">すべて</option>
                    <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                </select>
            </div>
            <div class="min-w-[220px] flex-1">
                <label class="block text-xs font-medium text-zinc-400">キーワード</label>
                <input
                    v-model="q"
                    type="text"
                    placeholder="名前・メール・SUNO IDで検索"
                    class="mt-1 w-full rounded-none border border-zinc-700 bg-zinc-900 px-3 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:border-brand-500 focus:outline-none"
                    @keyup.enter="applyFilters"
                />
            </div>
            <button
                class="rounded-full bg-brand-500 px-4 py-2 text-sm font-semibold text-black transition hover:bg-brand-400"
                @click="applyFilters"
            >
                検索
            </button>
            <button
                v-if="status || q"
                class="px-2 py-2 text-sm text-zinc-400 transition hover:text-white"
                @click="clearFilters"
            >
                クリア
            </button>
        </div>

        <div class="mt-6 overflow-x-auto border border-zinc-800">
            <table class="w-full text-sm">
                <thead class="bg-zinc-900 text-left text-zinc-400">
                    <tr>
                        <th class="p-3 font-medium">名前</th>
                        <th class="p-3 font-medium">メール</th>
                        <th class="p-3 font-medium">SUNO ID</th>
                        <th class="p-3 font-medium">志望動機</th>
                        <th class="p-3 font-medium">申込日</th>
                        <th class="p-3 font-medium">ステータス</th>
                        <th class="p-3 font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <tr v-for="a in applications" :key="a.id" class="align-top">
                        <td class="p-3 font-medium">{{ a.name }}</td>
                        <td class="p-3 text-zinc-400">{{ a.email }}</td>
                        <td class="p-3 text-zinc-400">{{ a.suno_id }}</td>
                        <td class="p-3">
                            <p class="max-w-md text-xs text-zinc-500">{{ a.motivation }}</p>
                        </td>
                        <td class="p-3 whitespace-nowrap text-xs text-zinc-500">{{ formatDate(a.created_at) }}</td>
                        <td class="p-3">
                            <select
                                :value="a.status"
                                class="rounded-none border border-zinc-700 bg-zinc-900 px-2 py-1 text-zinc-100 focus:border-brand-500 focus:outline-none"
                                @change="updateStatus(a.id, $event.target.value)"
                            >
                                <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                            </select>
                        </td>
                        <td class="p-3 whitespace-nowrap">
                            <span v-if="a.approved_user_id" class="text-xs text-zinc-500">登録済み</span>
                            <Link
                                v-else
                                :href="route('admin.reviewers.create', { application_id: a.id })"
                                class="text-brand-400 transition hover:text-brand-300"
                            >
                                レビュワーにする
                            </Link>
                        </td>
                    </tr>
                    <tr v-if="! applications.length">
                        <td colspan="7" class="p-4 text-zinc-500">申込みはまだありません。</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
