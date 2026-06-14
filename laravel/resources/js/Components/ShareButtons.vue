<script setup>
import { ref } from 'vue';

const props = defineProps({
    url: { type: String, required: true },
    title: { type: String, default: '' },
});

const copied = ref(false);
const e = encodeURIComponent;

const links = {
    x: `https://twitter.com/intent/tweet?text=${e(props.title)}&url=${e(props.url)}`,
    line: `https://social-plugins.line.me/lineit/share?url=${e(props.url)}`,
    facebook: `https://www.facebook.com/sharer/sharer.php?u=${e(props.url)}`,
};

const copyLink = async () => {
    try {
        await navigator.clipboard.writeText(props.url);
        copied.value = true;
        setTimeout(() => (copied.value = false), 1500);
    } catch (err) {
        // クリップボード非対応時は無視
    }
};

const btnClass =
    'flex h-9 w-9 items-center justify-center border border-zinc-700 text-zinc-300 transition hover:border-brand-500 hover:text-brand-400';
</script>

<template>
    <div class="flex items-center gap-2">
        <span class="mr-1 text-xs text-zinc-500">シェア</span>

        <a :href="links.x" target="_blank" rel="noopener" :class="btnClass" aria-label="Xでシェア">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" /></svg>
        </a>

        <a :href="links.line" target="_blank" rel="noopener" :class="btnClass" aria-label="LINEでシェア">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 5.69 2 10.23c0 4.07 3.55 7.48 8.35 8.12.33.07.77.22.88.5.1.26.07.66.03.92l-.14.85c-.04.25-.2 1 .88.54 1.08-.45 5.8-3.42 7.92-5.85C21.2 13.7 22 12.04 22 10.23 22 5.69 17.52 2 12 2zM8.2 12.5H6.05c-.2 0-.36-.16-.36-.36V8.06c0-.2.16-.36.36-.36.2 0 .36.16.36.36v3.72H8.2c.2 0 .36.16.36.36s-.16.36-.36.36zm1.5-.36c0 .2-.16.36-.36.36s-.36-.16-.36-.36V8.06c0-.2.16-.36.36-.36s.36.16.36.36v4.08zm4.92 0c0 .16-.1.3-.25.35l-.11.02c-.11 0-.22-.05-.29-.15l-2.12-2.88v2.66c0 .2-.16.36-.36.36s-.36-.16-.36-.36V8.06c0-.16.1-.3.25-.34l.11-.02c.1 0 .22.06.28.15l2.13 2.88V8.06c0-.2.16-.36.36-.36s.36.16.36.36v4.08zm3.2-2.4c.2 0 .36.16.36.36s-.16.36-.36.36h-1.78v1.32h1.78c.2 0 .36.16.36.36s-.16.36-.36.36h-2.14c-.2 0-.36-.16-.36-.36V8.06c0-.2.16-.36.36-.36h2.14c.2 0 .36.16.36.36s-.16.36-.36.36h-1.78v1.32h1.78z" /></svg>
        </a>

        <a :href="links.facebook" target="_blank" rel="noopener" :class="btnClass" aria-label="Facebookでシェア">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.07C24 5.41 18.63 0 12 0S0 5.4 0 12.07C0 18.1 4.39 23.1 10.13 24v-8.44H7.08v-3.49h3.04V9.41c0-3.02 1.8-4.7 4.54-4.7 1.31 0 2.68.24 2.68.24v2.97h-1.5c-1.5 0-1.96.93-1.96 1.89v2.26h3.32l-.53 3.49h-2.79V24C19.61 23.1 24 18.1 24 12.07z" /></svg>
        </a>

        <button type="button" :class="btnClass" aria-label="リンクをコピー" @click="copyLink">
            <svg v-if="!copied" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71" /><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71" /></svg>
            <svg v-else class="h-4 w-4 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5" /></svg>
        </button>
    </div>
</template>
