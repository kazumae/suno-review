<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    reviewer: { type: Object, required: true },
});

const inputClass =
    'mt-1 w-full rounded-none border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100 placeholder-zinc-600 focus:border-brand-500 focus:outline-none focus:ring-0';

const infoForm = useForm({
    email: props.reviewer.email,
    role: props.reviewer.role,
});

const passwordForm = useForm({
    password: '',
    password_confirmation: '',
});

const submitInfo = () =>
    infoForm.put(route('admin.reviewers.update', props.reviewer.id));

const submitPassword = () =>
    passwordForm.put(route('admin.reviewers.password.update', props.reviewer.id), {
        onSuccess: () => passwordForm.reset(),
    });
</script>

<template>
    <Head :title="`${reviewer.name} の編集 - Studio`" />

    <AdminLayout>
        <div class="flex items-center gap-3">
            <Link :href="route('admin.reviewers.index')" class="text-zinc-400 hover:text-zinc-100">← 一覧</Link>
            <h1 class="text-2xl font-bold">{{ reviewer.name }}</h1>
            <span class="text-sm text-zinc-500">{{ reviewer.role === 'admin' ? '管理者' : 'レビュワー' }} &middot; {{ reviewer.reviews_count }} レビュー</span>
        </div>

        <div class="mt-8 max-w-lg space-y-10">
            <!-- メール・権限 -->
            <section>
                <h2 class="mb-4 border-b border-zinc-800 pb-2 text-sm font-semibold uppercase tracking-widest text-zinc-400">メールアドレス・権限</h2>
                <form class="space-y-5" @submit.prevent="submitInfo">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300">メールアドレス <span class="text-brand-500">*</span></label>
                        <input v-model="infoForm.email" type="email" :class="inputClass" />
                        <p v-if="infoForm.errors.email" class="mt-1 text-sm text-red-400">{{ infoForm.errors.email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300">権限 <span class="text-brand-500">*</span></label>
                        <select v-model="infoForm.role" :class="inputClass">
                            <option value="reviewer">レビュワー</option>
                            <option value="admin">管理者</option>
                        </select>
                        <p v-if="infoForm.errors.role" class="mt-1 text-sm text-red-400">{{ infoForm.errors.role }}</p>
                    </div>
                    <button
                        type="submit"
                        :disabled="infoForm.processing"
                        class="rounded-full bg-brand-500 px-5 py-2 text-sm font-semibold text-black transition hover:bg-brand-400 disabled:opacity-50"
                    >
                        更新する
                    </button>
                </form>
            </section>

            <!-- パスワード -->
            <section>
                <h2 class="mb-4 border-b border-zinc-800 pb-2 text-sm font-semibold uppercase tracking-widest text-zinc-400">パスワード変更</h2>
                <form class="space-y-5" @submit.prevent="submitPassword">
                    <div>
                        <label class="block text-sm font-medium text-zinc-300">新しいパスワード <span class="text-brand-500">*</span></label>
                        <input v-model="passwordForm.password" type="password" autocomplete="new-password" :class="inputClass" />
                        <p v-if="passwordForm.errors.password" class="mt-1 text-sm text-red-400">{{ passwordForm.errors.password }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-300">確認用パスワード <span class="text-brand-500">*</span></label>
                        <input v-model="passwordForm.password_confirmation" type="password" autocomplete="new-password" :class="inputClass" />
                    </div>
                    <button
                        type="submit"
                        :disabled="passwordForm.processing"
                        class="rounded-full bg-brand-500 px-5 py-2 text-sm font-semibold text-black transition hover:bg-brand-400 disabled:opacity-50"
                    >
                        変更する
                    </button>
                </form>
            </section>
        </div>
    </AdminLayout>
</template>
