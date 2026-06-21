<script setup>
import { onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    processing: { type: Boolean, default: false },
});

const emit = defineEmits(['confirm', 'cancel']);

const notices = [
    {
        title: '必ずレビューされるとは限りません',
        body: 'いただいた楽曲は編集部とレビュワーが選定します。すべての依頼がレビュー・掲載されるわけではありません。',
    },
    {
        title: 'レビューには点数（スコア）がつきます',
        body: 'レビュワーが楽曲を評価し、点数を添えて公開します。',
    },
    {
        title: 'レビューは厳しくなる場合があります',
        body: '率直さを大切にしているため、時に厳しい指摘を含む内容になることがあります。',
    },
    {
        title: '公開されたレビューは削除できません',
        body: '一度作成・公開されたレビューは取り下げ・削除できません。あらかじめご了承ください。',
    },
];

const cancel = () => {
    if (!props.processing) {
        emit('cancel');
    }
};

const closeOnEscape = (e) => {
    if (e.key === 'Escape' && props.show) {
        e.preventDefault();
        cancel();
    }
};

watch(
    () => props.show,
    (val) => {
        document.body.style.overflow = val ? 'hidden' : '';
    },
);

onMounted(() => document.addEventListener('keydown', closeOnEscape));

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    document.body.style.overflow = '';
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/80" @click="cancel" />

                <div
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="request-notice-title"
                    class="relative z-10 max-h-[90vh] w-full overflow-y-auto border border-zinc-800 bg-zinc-950 p-6 sm:max-w-lg sm:p-8"
                >
                    <h2 id="request-notice-title" class="text-xl font-bold text-zinc-100">
                        レビューを依頼する前に
                    </h2>
                    <p class="mt-2 text-sm text-zinc-400">
                        以下の内容をご確認・ご了承の上で送信してください。
                    </p>

                    <ul class="mt-6 space-y-4">
                        <li v-for="(n, i) in notices" :key="i" class="flex gap-3">
                            <span class="mt-0.5 select-none font-mono text-sm font-bold text-brand-500">
                                {{ String(i + 1).padStart(2, '0') }}
                            </span>
                            <div>
                                <p class="font-semibold text-zinc-100">{{ n.title }}</p>
                                <p class="mt-1 text-sm leading-relaxed text-zinc-400">{{ n.body }}</p>
                            </div>
                        </li>
                    </ul>

                    <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button
                            type="button"
                            :disabled="processing"
                            class="rounded-full border border-zinc-700 px-6 py-2.5 font-medium text-zinc-300 transition hover:bg-zinc-900 disabled:opacity-50"
                            @click="cancel"
                        >
                            キャンセル
                        </button>
                        <button
                            type="button"
                            :disabled="processing"
                            class="rounded-full bg-brand-500 px-6 py-2.5 font-semibold text-black transition hover:bg-brand-400 disabled:opacity-50"
                            @click="emit('confirm')"
                        >
                            {{ processing ? '送信中…' : '理解して依頼する' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
