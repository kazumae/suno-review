<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    review: { type: Object, default: null },
    songs: { type: Array, default: () => [] },
    songId: { type: [Number, String, null], default: null },
    submitLabel: { type: String, default: '保存' },
});

const isEdit = !! props.review;
const src = props.review || {};

const form = useForm({
    song_id: src.song_id || props.songId || '',
    title: src.title || '',
    body: src.body || '',
    score_melody: src.score_melody || '',
    score_lyrics: src.score_lyrics || '',
    score_production: src.score_production || '',
    score_originality: src.score_originality || '',
    cover: null,
    // 新規は公開、編集は現状の公開状態を引き継ぐ
    published: isEdit ? !! src.published_at : true,
});

const scoreFields = [
    { key: 'score_melody', label: 'メロディ' },
    { key: 'score_lyrics', label: '歌詞' },
    { key: 'score_production', label: 'プロダクション' },
    { key: 'score_originality', label: '独自性' },
];

const inputClass =
    'mt-1 w-full rounded-none border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100 placeholder-zinc-600 focus:border-brand-500 focus:outline-none focus:ring-0';

const onFile = (e) => {
    form.cover = e.target.files[0] || null;
};

const submit = () => {
    if (isEdit) {
        form.transform((d) => ({ ...d, _method: 'put' })).post(route('admin.reviews.update', props.review.slug), { forceFormData: true });
    } else {
        form.post(route('admin.reviews.store'), { forceFormData: true });
    }
};
</script>

<template>
    <form class="max-w-2xl space-y-5" @submit.prevent="submit">
        <div>
            <label class="block text-sm font-medium text-zinc-300">対象の楽曲 <span class="text-brand-500">*</span></label>
            <select v-model="form.song_id" :class="inputClass">
                <option value="">選択してください</option>
                <option v-for="s in songs" :key="s.id" :value="s.id">{{ s.title }} / {{ s.artist_name }}</option>
            </select>
            <p v-if="form.errors.song_id" class="mt-1 text-sm text-red-400">{{ form.errors.song_id }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-300">レビュータイトル <span class="text-brand-500">*</span></label>
            <input v-model="form.title" type="text" :class="inputClass" />
            <p v-if="form.errors.title" class="mt-1 text-sm text-red-400">{{ form.errors.title }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-300">スコア（各5点満点・任意）</label>
            <div class="mt-1 grid grid-cols-2 gap-4">
                <div v-for="f in scoreFields" :key="f.key">
                    <label class="block text-xs text-zinc-400">{{ f.label }}</label>
                    <select v-model="form[f.key]" :class="inputClass">
                        <option value="">—</option>
                        <option v-for="n in 5" :key="n" :value="n">{{ n }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-300">本文 <span class="text-brand-500">*</span></label>
            <textarea v-model="form.body" rows="10" :class="inputClass" placeholder="空行で段落が分かれます。"></textarea>
            <p v-if="form.errors.body" class="mt-1 text-sm text-red-400">{{ form.errors.body }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-300">カバー画像（note式・任意）</label>
            <div v-if="review?.cover_url" class="mt-2 aspect-video w-64 overflow-hidden bg-zinc-800 ring-1 ring-zinc-800">
                <img :src="review.cover_url" class="h-full w-full object-cover" />
            </div>
            <input type="file" accept="image/*" class="mt-2 block text-sm text-zinc-400 file:mr-3 file:border-0 file:bg-zinc-800 file:px-3 file:py-2 file:text-zinc-200" @change="onFile" />
            <p v-if="form.progress" class="mt-1 text-xs text-zinc-500">アップロード中 {{ form.progress.percentage }}%</p>
            <p v-if="form.errors.cover" class="mt-1 text-sm text-red-400">{{ form.errors.cover }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-300">公開状態</label>
            <select v-model="form.published" :class="inputClass">
                <option :value="true">公開</option>
                <option :value="false">非公開（下書き）</option>
            </select>
            <p class="mt-1 text-xs text-zinc-500">非公開にすると公開ページ・ランキングから見えなくなります。</p>
        </div>

        <button
            type="submit"
            :disabled="form.processing"
            class="rounded-full bg-brand-500 px-6 py-3 font-semibold text-black transition hover:bg-brand-400 disabled:opacity-50"
        >
            {{ submitLabel }}
        </button>
    </form>
</template>
