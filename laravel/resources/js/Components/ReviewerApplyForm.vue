<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const done = ref(false);

const inputClass =
    'mt-1 w-full rounded-none border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100 placeholder-zinc-600 focus:border-brand-500 focus:outline-none focus:ring-0';

const form = useForm({
    name: '',
    email: '',
    suno_id: '',
    motivation: '',
    inline: true,
});

const submit = () => {
    form.post(route('applications.store'), {
        preserveScroll: true,
        onSuccess: () => {
            done.value = true;
            form.reset();
        },
    });
};
</script>

<template>
    <div class="border border-zinc-800 bg-zinc-950 p-6 md:p-8">
        <div v-if="done" class="py-12 text-center">
            <p class="text-2xl font-bold text-brand-500">申込みありがとうございました！</p>
            <p class="mt-3 text-zinc-400">編集部で確認のうえ、折り返しご連絡します。</p>
        </div>

        <form v-else class="space-y-4" @submit.prevent="submit">
            <div>
                <label class="block text-sm font-medium text-zinc-300">お名前 <span class="text-brand-500">*</span></label>
                <input v-model="form.name" type="text" required :class="inputClass" />
                <p class="mt-1 text-xs text-zinc-500">SUNOの表示名と同じ名前でお願いします。</p>
                <p v-if="form.errors.name" class="mt-1 text-sm text-red-400">{{ form.errors.name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300">メールアドレス <span class="text-brand-500">*</span></label>
                <input v-model="form.email" type="email" required placeholder="your@email.com" :class="inputClass" />
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-400">{{ form.errors.email }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300">SUNOアカウントID <span class="text-brand-500">*</span></label>
                <input v-model="form.suno_id" type="text" required placeholder="@username" :class="inputClass" />
                <p class="mt-1 text-xs text-zinc-500">SUNOアカウントのID（@から始まるユーザー名）。アカウントをお持ちの方が対象です。</p>
                <p v-if="form.errors.suno_id" class="mt-1 text-sm text-red-400">{{ form.errors.suno_id }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300">志望動機・書きたいこと <span class="text-brand-500">*</span></label>
                <textarea
                    v-model="form.motivation"
                    rows="5"
                    required
                    placeholder="なぜレビュワーをやりたいか、どんなレビューを書きたいか教えてください。"
                    :class="inputClass"
                ></textarea>
                <p v-if="form.errors.motivation" class="mt-1 text-sm text-red-400">{{ form.errors.motivation }}</p>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="rounded-full bg-brand-500 px-6 py-3 font-semibold text-black transition hover:bg-brand-400 disabled:opacity-50"
            >
                {{ form.processing ? '送信中…' : 'レビュワーに申し込む' }}
            </button>
        </form>
    </div>
</template>
