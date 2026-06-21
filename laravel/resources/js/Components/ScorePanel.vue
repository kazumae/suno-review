<script setup>
defineProps({
    review: { type: Object, required: true },
});
</script>

<template>
    <div v-if="review.overall_score != null" class="bg-zinc-900 p-5 ring-1 ring-zinc-800">
        <div class="flex items-baseline gap-2">
            <span class="text-4xl font-bold text-brand-500">{{ review.overall_score }}</span>
            <span class="text-sm text-zinc-500">/ 100</span>
        </div>
        <div class="mt-3 h-1.5 bg-zinc-800">
            <div class="h-full bg-brand-500" :style="{ width: review.overall_score + '%' }"></div>
        </div>
    </div>
</template>
