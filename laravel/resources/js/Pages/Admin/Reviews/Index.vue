<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    reviews: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="レビュー - Studio" />

    <AdminLayout>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">レビュー</h1>
            <Link :href="route('admin.reviews.create')" class="rounded-full bg-brand-500 px-4 py-2 text-sm font-semibold text-black transition hover:bg-brand-400">
                ＋ レビューを書く
            </Link>
        </div>

        <div class="mt-6 overflow-x-auto border border-zinc-800">
            <table class="w-full text-sm">
                <thead class="bg-zinc-900 text-left text-zinc-400">
                    <tr>
                        <th class="p-3 font-medium">タイトル</th>
                        <th class="p-3 font-medium">対象曲</th>
                        <th class="p-3 font-medium">レビュワー</th>
                        <th class="p-3 font-medium">点数</th>
                        <th class="p-3 font-medium">状態</th>
                        <th class="p-3 font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <tr
                        v-for="review in reviews"
                        :key="review.id"
                        class="cursor-pointer transition hover:bg-zinc-900"
                        @click="router.visit(route('admin.reviews.edit', review.slug))"
                    >
                        <td class="p-3 font-medium">{{ review.title }}</td>
                        <td class="p-3 text-zinc-400">{{ review.song?.title }}</td>
                        <td class="p-3 text-zinc-400">{{ review.reviewer?.name }}</td>
                        <td class="p-3 text-brand-400">{{ review.overall_score ? Number(review.overall_score).toFixed(1) : '—' }}</td>
                        <td class="p-3">
                            <span :class="review.published_at ? 'text-brand-400' : 'text-zinc-500'">
                                {{ review.published_at ? '公開' : '非公開' }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <Link :href="route('admin.reviews.edit', review.slug)" class="text-brand-400 transition hover:text-brand-300" @click.stop>編集</Link>
                        </td>
                    </tr>
                    <tr v-if="! reviews.length">
                        <td colspan="6" class="p-4 text-zinc-500">レビューがありません。</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
