<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    songs: { type: Array, default: () => [] },
    slots: { type: Object, default: () => ({}) },
});

const slotMeta = [
    { pos: 1, label: 'スロット1', hint: '左の大きい枠（縦長）' },
    { pos: 2, label: 'スロット2', hint: '右上の横長' },
    { pos: 3, label: 'スロット3', hint: '右下スクエア（左）' },
    { pos: 4, label: 'スロット4', hint: '右下スクエア（右）' },
];

const form = useForm({
    slots: {
        1: props.slots[1] || '',
        2: props.slots[2] || '',
        3: props.slots[3] || '',
        4: props.slots[4] || '',
    },
});

const inputClass =
    'mt-1 w-full rounded-none border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100 focus:border-brand-500 focus:outline-none focus:ring-0';

const submit = () => form.put(route('admin.featured.update'));
</script>

<template>
    <Head title="メインビジュアル - Studio" />

    <AdminLayout>
        <h1 class="text-2xl font-bold">メインビジュアル（編集部ピック）</h1>
        <p class="mt-1 max-w-2xl text-sm text-zinc-400">
            トップ最上部のモザイクに表示する4曲を選びます。未選択のスロットはランキング上位で自動補完されます。
        </p>

        <!-- レイアウトの目安 -->
        <div class="mt-6 grid max-w-md grid-cols-3 grid-rows-2 gap-1 text-xs text-zinc-500">
            <div class="row-span-2 flex items-center justify-center border border-zinc-700 bg-zinc-900 py-6">1</div>
            <div class="col-span-2 flex items-center justify-center border border-zinc-700 bg-zinc-900 py-3">2</div>
            <div class="flex items-center justify-center border border-zinc-700 bg-zinc-900 py-3">3</div>
            <div class="flex items-center justify-center border border-zinc-700 bg-zinc-900 py-3">4</div>
        </div>

        <form class="mt-6 max-w-xl space-y-5" @submit.prevent="submit">
            <div v-for="s in slotMeta" :key="s.pos">
                <label class="block text-sm font-medium text-zinc-300">
                    {{ s.label }} <span class="text-zinc-500">— {{ s.hint }}</span>
                </label>
                <select v-model="form.slots[s.pos]" :class="inputClass">
                    <option value="">（未選択 → ランキングで自動補完）</option>
                    <option v-for="song in songs" :key="song.id" :value="song.id">{{ song.title }} / {{ song.artist_name }}</option>
                </select>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="rounded-full bg-brand-500 px-6 py-3 font-semibold text-black transition hover:bg-brand-400 disabled:opacity-50"
            >
                保存する
            </button>
        </form>
    </AdminLayout>
</template>
