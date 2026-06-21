<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);
const isAdmin = computed(() => user.value?.role === 'admin');

const items = [
    { label: 'ダッシュボード', name: 'admin.dashboard' },
    { label: 'メインビジュアル', name: 'admin.featured.index', adminOnly: true },
    { label: 'レビュー依頼', name: 'admin.requests.index' },
    { label: '楽曲', name: 'admin.songs.index' },
    { label: 'レビュー', name: 'admin.reviews.index' },
    { label: 'レビュワー申込み', name: 'admin.applications.index', adminOnly: true },
    { label: 'レビュワー', name: 'admin.reviewers.index', adminOnly: true },
];
const nav = computed(() => items.filter((i) => route().has(i.name) && (! i.adminOnly || isAdmin.value)));

const logout = () => router.post(route('logout'));
</script>

<template>
    <div class="flex min-h-screen bg-zinc-950 text-zinc-100 antialiased">
        <!-- Sidebar -->
        <aside class="hidden w-56 shrink-0 border-r border-zinc-800 md:block">
            <div class="flex h-16 items-center px-5 text-lg font-bold">SUNO <span class="text-brand-500">&nbsp;Studio</span></div>
            <nav class="space-y-1 px-3 py-4 text-sm">
                <Link
                    v-for="i in nav"
                    :key="i.name"
                    :href="route(i.name)"
                    :class="[
                        'block px-3 py-2 transition',
                        route().current(i.name) ? 'bg-brand-500 font-semibold text-black' : 'text-zinc-300 hover:bg-zinc-900',
                    ]"
                >
                    {{ i.label }}
                </Link>
            </nav>
        </aside>

        <!-- Main -->
        <div class="flex min-w-0 flex-1 flex-col">
            <header class="flex h-16 items-center gap-4 border-b border-zinc-800 px-5">
                <Link :href="route('home')" class="text-sm text-zinc-400 transition hover:text-white">← サイトを見る</Link>
                <div class="ml-auto flex items-center gap-4 text-sm">
                    <Link
                        :href="route('profile.edit')"
                        :class="[
                            'transition hover:text-white',
                            route().current('profile.edit') ? 'text-white' : 'text-zinc-400',
                        ]"
                    >
                        {{ user?.name }}（{{ isAdmin ? '管理者' : 'レビュワー' }}）
                    </Link>
                    <button class="text-zinc-400 transition hover:text-white" @click="logout">ログアウト</button>
                </div>
            </header>

            <div v-if="flashSuccess" class="border-b border-brand-500/30 bg-brand-500/10 px-5 py-3 text-sm text-brand-300">{{ flashSuccess }}</div>
            <div v-if="flashError" class="border-b border-red-500/30 bg-red-500/10 px-5 py-3 text-sm text-red-300">{{ flashError }}</div>

            <main class="flex-1 p-5 md:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
