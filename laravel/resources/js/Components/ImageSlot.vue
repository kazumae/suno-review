<script setup>
defineProps({
    caption: { type: String, required: true },
    size: { type: String, default: '' },
    ratio: { type: String, default: 'aspect-[4/3]' },
});
</script>

<template>
    <div :class="['flex w-full items-center justify-center border border-dashed border-zinc-700 bg-zinc-900/40 p-6', ratio]">
        <div class="max-w-sm text-center">
            <div class="mb-3 inline-block border border-zinc-700 px-2 py-0.5 text-[10px] uppercase tracking-[0.2em] text-zinc-500">
                Image
            </div>
            <p class="text-sm leading-relaxed text-zinc-400">{{ caption }}</p>
            <p v-if="size" class="mt-2 text-xs text-zinc-600">推奨サイズ {{ size }}</p>
        </div>
    </div>
</template>
