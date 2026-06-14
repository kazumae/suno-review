<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    songs: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="楽曲 - Studio" />

    <AdminLayout>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">楽曲</h1>
            <Link :href="route('admin.songs.create')" class="rounded-full bg-brand-500 px-4 py-2 text-sm font-semibold text-black transition hover:bg-brand-400">
                ＋ 楽曲を登録
            </Link>
        </div>

        <div class="mt-6 overflow-x-auto border border-zinc-800">
            <table class="w-full text-sm">
                <thead class="bg-zinc-900 text-left text-zinc-400">
                    <tr>
                        <th class="p-3 font-medium">タイトル</th>
                        <th class="p-3 font-medium">ジャンル</th>
                        <th class="p-3 font-medium">状態</th>
                        <th class="p-3 font-medium">レビュー</th>
                        <th class="p-3 font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <tr
                        v-for="song in songs"
                        :key="song.id"
                        class="cursor-pointer transition hover:bg-zinc-900"
                        @click="router.visit(route('admin.songs.edit', song.id))"
                    >
                        <td class="p-3">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-16 shrink-0 overflow-hidden bg-zinc-800">
                                    <img v-if="song.cover_url" :src="song.cover_url" class="h-full w-full object-cover" />
                                </div>
                                <div class="min-w-0">
                                    <div class="truncate font-medium">{{ song.title }}</div>
                                    <div class="truncate text-xs text-zinc-500">{{ song.artist_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-3 text-zinc-400">{{ song.genre || '—' }}</td>
                        <td class="p-3">
                            <span :class="song.status === 'published' ? 'text-brand-400' : 'text-zinc-500'">
                                {{ song.status === 'published' ? '公開' : '下書き' }}
                            </span>
                        </td>
                        <td class="p-3 text-zinc-400">{{ song.reviews_count }}</td>
                        <td class="p-3 text-right whitespace-nowrap">
                            <Link :href="route('admin.reviews.create', { song_id: song.id })" class="text-brand-400 transition hover:text-brand-300" @click.stop>レビュー</Link>
                            <Link :href="route('admin.songs.edit', song.id)" class="ml-3 text-zinc-400 transition hover:text-white" @click.stop>編集</Link>
                        </td>
                    </tr>
                    <tr v-if="! songs.length">
                        <td colspan="5" class="p-4 text-zinc-500">楽曲がありません。</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
