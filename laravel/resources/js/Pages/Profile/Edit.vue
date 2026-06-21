<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="アカウント設定 - Studio" />

    <AdminLayout>
        <h1 class="text-2xl font-bold">アカウント設定</h1>
        <p class="mt-1 text-sm text-zinc-400">ログインに使うメールアドレスとパスワードを変更できます。</p>

        <div class="mt-8 space-y-12">
            <UpdateProfileInformationForm />
            <div class="border-t border-zinc-800"></div>
            <UpdatePasswordForm />
        </div>
    </AdminLayout>
</template>
