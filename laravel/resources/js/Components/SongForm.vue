<script setup>
import { useForm } from '@inertiajs/vue3';
import TagInput from '@/Components/TagInput.vue';

const props = defineProps({
    song: { type: Object, default: null },
    prefill: { type: Object, default: null },
    submitLabel: { type: String, default: '保存' },
});

const isEdit = !! props.song;
const src = props.song || props.prefill || {};

const form = useForm({
    title: src.title || '',
    artist_name: src.artist_name || '',
    suno_url: src.suno_url || '',
    youtube_url: src.youtube_url || '',
    genre: src.genre || '',
    tags: Array.isArray(src.tags) ? [...src.tags] : [],
    description: src.description || '',
    status: src.status || 'published',
    cover: null,
    request_id: props.prefill?.request_id || null,
});

const inputClass =
    'mt-1 w-full rounded-none border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100 placeholder-zinc-600 focus:border-brand-500 focus:outline-none focus:ring-0';

const onFile = (e) => {
    form.cover = e.target.files[0] || null;
};

const submit = () => {
    if (isEdit) {
        form.transform((d) => ({ ...d, _method: 'put' })).post(route('admin.songs.update', props.song.id), { forceFormData: true });
    } else {
        form.post(route('admin.songs.store'), { forceFormData: true });
    }
};
</script>

<template>
    <form class="max-w-2xl space-y-5" @submit.prevent="submit">
        <div>
            <label class="block text-sm font-medium text-zinc-300">タイトル <span class="text-brand-500">*</span></label>
            <input v-model="form.title" type="text" :class="inputClass" />
            <p v-if="form.errors.title" class="mt-1 text-sm text-red-400">{{ form.errors.title }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-300">アーティスト名 <span class="text-brand-500">*</span></label>
            <input v-model="form.artist_name" type="text" :class="inputClass" />
            <p v-if="form.errors.artist_name" class="mt-1 text-sm text-red-400">{{ form.errors.artist_name }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-300">SUNO URL <span class="text-brand-500">*</span></label>
            <input v-model="form.suno_url" type="url" placeholder="https://suno.com/song/..." :class="inputClass" />
            <p v-if="form.errors.suno_url" class="mt-1 text-sm text-red-400">{{ form.errors.suno_url }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-300">YouTube URL（任意）</label>
            <input v-model="form.youtube_url" type="url" placeholder="https://www.youtube.com/watch?v=..." :class="inputClass" />
            <p v-if="form.errors.youtube_url" class="mt-1 text-sm text-red-400">{{ form.errors.youtube_url }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-zinc-300">ジャンル</label>
                <input v-model="form.genre" type="text" placeholder="City Pop など" :class="inputClass" />
                <p v-if="form.errors.genre" class="mt-1 text-sm text-red-400">{{ form.errors.genre }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-300">公開状態</label>
                <select v-model="form.status" :class="inputClass">
                    <option value="published">公開</option>
                    <option value="pending">非公開（下書き）</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-300">タグ</label>
            <TagInput v-model="form.tags" />
            <p v-if="form.errors.tags" class="mt-1 text-sm text-red-400">{{ form.errors.tags }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-300">曲紹介</label>
            <textarea v-model="form.description" rows="5" :class="inputClass"></textarea>
            <p v-if="form.errors.description" class="mt-1 text-sm text-red-400">{{ form.errors.description }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-zinc-300">カバー画像</label>
            <div v-if="song?.cover_url" class="mt-2 aspect-video w-64 overflow-hidden bg-zinc-800 ring-1 ring-zinc-800">
                <img :src="song.cover_url" class="h-full w-full object-cover" />
            </div>
            <input type="file" accept="image/*" class="mt-2 block text-sm text-zinc-400 file:mr-3 file:border-0 file:bg-zinc-800 file:px-3 file:py-2 file:text-zinc-200" @change="onFile" />
            <p v-if="form.progress" class="mt-1 text-xs text-zinc-500">アップロード中 {{ form.progress.percentage }}%</p>
            <p v-if="form.errors.cover" class="mt-1 text-sm text-red-400">{{ form.errors.cover }}</p>
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
