<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import RequestNoticeModal from '@/Components/RequestNoticeModal.vue';

const done = ref(false);
const showNotice = ref(false);

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
    inline: true,
});

// レビュワーへのメッセージの記入例テンプレート。空欄のときだけ挿入でき、入力済みなら上書きしない
const noteTemplate = `【この曲で一番こだわった点】\n\n\n【制作時に特に意図したこと】\n\n\n【生成後に編集・調整した箇所（あれば）】\n\n`;

const insertNoteTemplate = () => {
    if (form.note.trim() === '') {
        form.note = noteTemplate;
    }
};

// HTML5バリデーション通過後に注意事項モーダルを表示する
const openNotice = () => {
    showNotice.value = true;
};

const submit = () => {
    form.post(route('requests.store'), {
        preserveScroll: true,
        onSuccess: () => {
            done.value = true;
            form.reset();
            showNotice.value = false;
        },
        onError: () => {
            showNotice.value = false;
        },
    });
};
</script>

<template>
    <div class="border border-zinc-800 bg-zinc-950 p-6 md:p-8">
        <div v-if="done" class="py-12 text-center">
            <p class="text-2xl font-bold text-brand-500">リクエストありがとうございました！</p>
            <p class="mt-3 text-zinc-400">編集部とレビュワーが確認します。レビュー公開まで少しお待ちください。</p>
        </div>

        <form v-else class="space-y-4" @submit.prevent="openNotice">
            <div>
                <label class="block text-sm font-medium text-zinc-300">SUNO URL <span class="text-brand-500">*</span></label>
                <input v-model="form.suno_url" type="url" required placeholder="https://suno.com/song/... または共有リンク" :class="inputClass" />
                <p v-if="form.errors.suno_url" class="mt-1 text-sm text-red-400">{{ form.errors.suno_url }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300">アーティストページURL <span class="text-brand-500">*</span></label>
                <input v-model="form.artist_url" type="url" required placeholder="https://suno.com/@yourname" :class="inputClass" />
                <p class="mt-1 text-xs text-zinc-500">あなたのSUNOプロフィールなど、アーティストのトップページ。楽曲ページからリンクします。</p>
                <p v-if="form.errors.artist_url" class="mt-1 text-sm text-red-400">{{ form.errors.artist_url }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300">YouTube URL（任意）</label>
                <input v-model="form.youtube_url" type="url" placeholder="https://www.youtube.com/watch?v=..." :class="inputClass" />
                <p v-if="form.errors.youtube_url" class="mt-1 text-sm text-red-400">{{ form.errors.youtube_url }}</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-zinc-300">タイトル（任意）</label>
                    <input v-model="form.title" type="text" :class="inputClass" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-300">ジャンル（任意）</label>
                    <select v-model="form.genre" :class="inputClass">
                        <option value="">選択してください</option>
                        <option v-for="g in genres" :key="g" :value="g">{{ g }}</option>
                    </select>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between gap-2">
                    <label class="block text-sm font-medium text-zinc-300">レビュワーへのメッセージ（任意）</label>
                    <button
                        type="button"
                        :disabled="form.note.trim() !== ''"
                        class="shrink-0 text-xs text-brand-500 hover:underline disabled:cursor-not-allowed disabled:text-zinc-600 disabled:no-underline"
                        @click="insertNoteTemplate"
                    >
                        記入例を挿入
                    </button>
                </div>
                <textarea v-model="form.note" rows="4" :class="inputClass"></textarea>
                <p class="mt-1 text-xs text-zinc-500">
                    曲の背景を教えてもらえると、レビュワーが意図を汲んだレビューを書きやすくなります。たとえば次のような内容です。
                </p>
                <ul class="mt-1 space-y-0.5 text-xs text-zinc-500">
                    <li>・この曲で一番こだわった点</li>
                    <li>・制作時に特に意図したこと</li>
                    <li>・生成後に編集・調整した箇所（あれば）</li>
                </ul>
                <p v-if="form.errors.note" class="mt-1 text-sm text-red-400">{{ form.errors.note }}</p>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="rounded-full bg-brand-500 px-6 py-3 font-semibold text-black transition hover:bg-brand-400 disabled:opacity-50"
            >
                {{ form.processing ? '送信中…' : 'レビューを依頼する' }}
            </button>
        </form>

        <RequestNoticeModal
            :show="showNotice"
            :processing="form.processing"
            @confirm="submit"
            @cancel="showNotice = false"
        />
    </div>
</template>
