<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

const inputClass =
    'mt-1 w-full rounded-none border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100 placeholder-zinc-600 focus:border-brand-500 focus:outline-none focus:ring-0';

const form = useForm({
    name: user.value.name,
    email: user.value.email,
    suno_url: user.value.suno_url || '',
    avatar: null,
});

const avatarPreview = ref(null);
const onAvatar = (e) => {
    const file = e.target.files[0] || null;
    form.avatar = file;
    avatarPreview.value = file ? URL.createObjectURL(file) : null;
};

// ファイルを含むため method spoofing で multipart 送信する
const submit = () =>
    form
        .transform((d) => ({ ...d, _method: 'patch' }))
        .post(route('profile.update'), { preserveScroll: true, forceFormData: true });

const resendForm = useForm({});
const resend = () => resendForm.post(route('profile.email.resend'), { preserveScroll: true });

const cancelForm = useForm({});
const cancel = () => cancelForm.delete(route('profile.email.cancel'), { preserveScroll: true });
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-semibold">プロフィール</h2>
            <p class="mt-1 text-sm text-zinc-400">名前とログイン用メールアドレスを変更できます。</p>
        </header>

        <form class="mt-6 max-w-lg space-y-5" @submit.prevent="submit">
            <div>
                <label class="block text-sm font-medium text-zinc-300">アイコン</label>
                <div class="mt-2 flex items-center gap-4">
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden bg-zinc-800 text-2xl font-bold text-zinc-500 ring-1 ring-zinc-700">
                        <img v-if="avatarPreview || user.avatar_url" :src="avatarPreview || user.avatar_url" alt="" class="h-full w-full object-cover" />
                        <span v-else>{{ user.name?.charAt(0) }}</span>
                    </div>
                    <div class="text-sm">
                        <input type="file" accept="image/*" class="block text-zinc-400 file:mr-3 file:border-0 file:bg-zinc-800 file:px-3 file:py-2 file:text-zinc-200" @change="onAvatar" />
                        <p class="mt-1 text-xs text-zinc-500">正方形・2MBまで。記事やプロフィールの著者表示に使われます。</p>
                    </div>
                </div>
                <p v-if="form.progress" class="mt-1 text-xs text-zinc-500">アップロード中 {{ form.progress.percentage }}%</p>
                <p v-if="form.errors.avatar" class="mt-1 text-sm text-red-400">{{ form.errors.avatar }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300">名前 <span class="text-brand-500">*</span></label>
                <input v-model="form.name" type="text" autocomplete="name" :class="inputClass" />
                <p v-if="form.errors.name" class="mt-1 text-sm text-red-400">{{ form.errors.name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300">メールアドレス <span class="text-brand-500">*</span></label>
                <input v-model="form.email" type="email" autocomplete="username" :class="inputClass" />
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-400">{{ form.errors.email }}</p>
                <p class="mt-1 text-xs text-zinc-500">
                    変更すると新しいアドレス宛に確認メールを送ります。リンクを開くまでは現在のアドレスでログインできます。
                </p>
            </div>

            <div v-if="user.pending_email" class="border border-brand-500/40 bg-brand-500/10 px-4 py-3 text-sm">
                <p class="text-brand-200">
                    <span class="font-semibold">{{ user.pending_email }}</span> への変更を確認中です。届いたメールのリンクを開くと完了します。
                </p>
                <div class="mt-2 flex items-center gap-4">
                    <button
                        type="button"
                        :disabled="resendForm.processing"
                        class="text-brand-300 underline transition hover:text-brand-200 disabled:opacity-50"
                        @click="resend"
                    >
                        確認メールを再送
                    </button>
                    <button
                        type="button"
                        :disabled="cancelForm.processing"
                        class="text-zinc-400 underline transition hover:text-zinc-200 disabled:opacity-50"
                        @click="cancel"
                    >
                        変更を取り消す
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300">SUNOアカウント（任意）</label>
                <input v-model="form.suno_url" type="url" placeholder="https://suno.com/@yourname" :class="inputClass" />
                <p class="mt-1 text-xs text-zinc-500">あなたのSUNOプロフィールURL。レビュワーページからリンクします。</p>
                <p v-if="form.errors.suno_url" class="mt-1 text-sm text-red-400">{{ form.errors.suno_url }}</p>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="rounded-full bg-brand-500 px-6 py-3 font-semibold text-black transition hover:bg-brand-400 disabled:opacity-50"
            >
                保存する
            </button>
        </form>
    </section>
</template>
