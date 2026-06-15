<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const genres = ['City Pop', 'Lo-fi', 'Ballad', 'Rock', 'EDM', 'Ambient', 'HipHop', 'J-Pop', 'その他'];

const inputClass =
    'mt-1 w-full rounded-none border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100 placeholder-zinc-600 focus:border-brand-500 focus:outline-none focus:ring-0';

const form = useForm({
    suno_url: '',
    artist_url: '',
    youtube_url: '',
    title: '',
    genre: '',
    note: '',
});

const submit = () => form.post(route('requests.store'));
</script>

<template>
    <Head title="レビュー依頼" />

    <PublicLayout>
        <div class="mx-auto max-w-2xl px-4 py-10">
            <h1 class="text-2xl font-bold">レビュー依頼</h1>
            <p class="mt-2 text-zinc-400">
                SUNOの楽曲URLを送ってください。編集部とレビュワーが聴いて、ストーリーを添えてレビューします。
            </p>

            <form class="mt-8 space-y-5" @submit.prevent="submit">
                <div>
                    <label class="block text-sm font-medium text-zinc-300">
                        SUNO URL <span class="text-brand-500">*</span>
                    </label>
                    <input v-model="form.suno_url" type="url" required placeholder="https://suno.com/song/..." :class="inputClass" />
                    <p v-if="form.errors.suno_url" class="mt-1 text-sm text-red-400">{{ form.errors.suno_url }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-300">
                        アーティストページURL <span class="text-brand-500">*</span>
                    </label>
                    <input v-model="form.artist_url" type="url" required placeholder="https://suno.com/@yourname" :class="inputClass" />
                    <p class="mt-1 text-xs text-zinc-500">あなたのSUNOプロフィールなど、アーティストのトップページ。楽曲ページからリンクします。</p>
                    <p v-if="form.errors.artist_url" class="mt-1 text-sm text-red-400">{{ form.errors.artist_url }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-300">YouTube URL（任意）</label>
                    <input v-model="form.youtube_url" type="url" placeholder="https://www.youtube.com/watch?v=..." :class="inputClass" />
                    <p v-if="form.errors.youtube_url" class="mt-1 text-sm text-red-400">{{ form.errors.youtube_url }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-300">タイトル（任意）</label>
                    <input v-model="form.title" type="text" :class="inputClass" />
                    <p v-if="form.errors.title" class="mt-1 text-sm text-red-400">{{ form.errors.title }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-300">ジャンル（任意）</label>
                    <select v-model="form.genre" :class="inputClass">
                        <option value="">選択してください</option>
                        <option v-for="g in genres" :key="g" :value="g">{{ g }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-300">レビュワーへのメッセージ（任意）</label>
                    <textarea v-model="form.note" rows="5" :class="inputClass"></textarea>
                    <p v-if="form.errors.note" class="mt-1 text-sm text-red-400">{{ form.errors.note }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-full bg-brand-500 px-6 py-3 font-semibold text-black transition hover:bg-brand-400 disabled:opacity-50"
                >
                    依頼を送信
                </button>
            </form>
        </div>
    </PublicLayout>
</template>
