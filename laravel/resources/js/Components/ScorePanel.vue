<script setup>
defineProps({
    review: { type: Object, required: true },
});

const dims = [
    { key: 'score_melody', label: 'メロディ' },
    { key: 'score_lyrics', label: '歌詞' },
    { key: 'score_production', label: 'プロダクション' },
    { key: 'score_originality', label: '独自性' },
];
</script>

<template>
    <div class="bg-zinc-900 p-5 ring-1 ring-zinc-800">
        <div class="flex items-baseline gap-2">
            <span class="text-3xl font-bold text-brand-500">{{ Number(review.overall_score).toFixed(1) }}</span>
            <span class="text-sm text-zinc-500">/ 5.0</span>
        </div>
        <div class="mt-4 space-y-3">
            <div v-for="d in dims" :key="d.key" v-show="review[d.key]" class="text-sm">
                <div class="flex justify-between text-zinc-400">
                    <span>{{ d.label }}</span>
                    <span>{{ review[d.key] }}</span>
                </div>
                <div class="mt-1 h-1.5 bg-zinc-800">
                    <div class="h-full bg-brand-500" :style="{ width: (review[d.key] / 5 * 100) + '%' }"></div>
                </div>
            </div>
        </div>
    </div>
</template>
