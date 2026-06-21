<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const inputClass =
    'mt-1 w-full rounded-none border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100 placeholder-zinc-600 focus:border-brand-500 focus:outline-none focus:ring-0';

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-semibold">パスワード</h2>
            <p class="mt-1 text-sm text-zinc-400">安全のため、推測されにくい長めのパスワードを設定してください。</p>
        </header>

        <form class="mt-6 max-w-lg space-y-5" @submit.prevent="updatePassword">
            <div>
                <label class="block text-sm font-medium text-zinc-300">現在のパスワード <span class="text-brand-500">*</span></label>
                <input
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    autocomplete="current-password"
                    :class="inputClass"
                />
                <p v-if="form.errors.current_password" class="mt-1 text-sm text-red-400">{{ form.errors.current_password }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300">新しいパスワード <span class="text-brand-500">*</span></label>
                <input
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    autocomplete="new-password"
                    :class="inputClass"
                />
                <p v-if="form.errors.password" class="mt-1 text-sm text-red-400">{{ form.errors.password }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-300">新しいパスワード（確認） <span class="text-brand-500">*</span></label>
                <input
                    v-model="form.password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    :class="inputClass"
                />
                <p v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-400">{{ form.errors.password_confirmation }}</p>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="rounded-full bg-brand-500 px-6 py-3 font-semibold text-black transition hover:bg-brand-400 disabled:opacity-50"
            >
                パスワードを変更
            </button>
        </form>
    </section>
</template>
