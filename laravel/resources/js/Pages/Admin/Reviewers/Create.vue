<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const inputClass =
    'mt-1 w-full rounded-none border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100 placeholder-zinc-600 focus:border-brand-500 focus:outline-none focus:ring-0';

const form = useForm({
    name: '',
    handle: '',
    email: '',
    password: '',
    bio: '',
});

const submit = () => form.post(route('admin.reviewers.store'));
</script>

<template>
    <Head title="レビュワーを招待 - Studio" />

    <AdminLayout>
        <h1 class="text-2xl font-bold">レビュワーを招待</h1>
        <p class="mt-1 text-sm text-zinc-400">アカウントを作成します。ログイン情報を本人に共有してください。</p>

        <form class="mt-6 max-w-lg space-y-5" @submit.prevent="submit">
            <div>
                <label class="block text-sm font-medium text-zinc-300">名前 <span class="text-brand-500">*</span></label>
                <input v-model="form.name" type="text" :class="inputClass" />
                <p v-if="form.errors.name" class="mt-1 text-sm text-red-400">{{ form.errors.name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-300">ハンドル（URL用・半角英数）<span class="text-brand-500">*</span></label>
                <input v-model="form.handle" type="text" placeholder="aoi" :class="inputClass" />
                <p v-if="form.errors.handle" class="mt-1 text-sm text-red-400">{{ form.errors.handle }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-300">メール <span class="text-brand-500">*</span></label>
                <input v-model="form.email" type="email" :class="inputClass" />
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-400">{{ form.errors.email }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-300">初期パスワード <span class="text-brand-500">*</span></label>
                <input v-model="form.password" type="text" :class="inputClass" />
                <p v-if="form.errors.password" class="mt-1 text-sm text-red-400">{{ form.errors.password }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-zinc-300">プロフィール（任意）</label>
                <textarea v-model="form.bio" rows="4" :class="inputClass"></textarea>
                <p v-if="form.errors.bio" class="mt-1 text-sm text-red-400">{{ form.errors.bio }}</p>
            </div>
            <button
                type="submit"
                :disabled="form.processing"
                class="rounded-full bg-brand-500 px-6 py-3 font-semibold text-black transition hover:bg-brand-400 disabled:opacity-50"
            >
                招待する
            </button>
        </form>
    </AdminLayout>
</template>
