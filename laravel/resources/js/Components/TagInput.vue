<script setup>
import { ref } from 'vue';

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    placeholder: { type: String, default: 'タグを入力して Enter' },
});

const emit = defineEmits(['update:modelValue']);

const input = ref('');

const addTag = () => {
    const v = input.value.replace(/,/g, '').trim();
    if (v && ! props.modelValue.includes(v)) {
        emit('update:modelValue', [...props.modelValue, v]);
    }
    input.value = '';
};

const removeTag = (i) => {
    const next = props.modelValue.slice();
    next.splice(i, 1);
    emit('update:modelValue', next);
};

const onKeydown = (e) => {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        addTag();
    } else if (e.key === 'Backspace' && input.value === '' && props.modelValue.length) {
        removeTag(props.modelValue.length - 1);
    }
};
</script>

<template>
    <div class="mt-1 flex flex-wrap items-center gap-2 border border-zinc-700 bg-zinc-900 p-2 focus-within:border-brand-500">
        <span
            v-for="(tag, i) in modelValue"
            :key="tag"
            class="inline-flex items-center gap-1 bg-zinc-800 px-2 py-1 text-sm text-zinc-200"
        >
            {{ tag }}
            <button type="button" class="leading-none text-zinc-500 transition hover:text-brand-400" @click="removeTag(i)">×</button>
        </span>
        <input
            v-model="input"
            type="text"
            :placeholder="modelValue.length ? '' : placeholder"
            class="min-w-[8rem] flex-1 border-0 bg-transparent p-1 text-zinc-100 placeholder-zinc-600 focus:outline-none focus:ring-0"
            @keydown="onKeydown"
            @blur="addTag"
        />
    </div>
</template>
