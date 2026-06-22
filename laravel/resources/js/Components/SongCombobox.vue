<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue';

const props = defineProps({
    modelValue: { type: [Number, String, null], default: '' },
    selectedSong: { type: Object, default: null },
    error: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

const root = ref(null);
const inputEl = ref(null);
const query = ref('');
const results = ref([]);
const open = ref(false);
const loading = ref(false);
const highlight = ref(-1);
const selected = ref(props.selectedSong);

let timer = null;

const fetchSongs = async () => {
    loading.value = true;
    try {
        const res = await fetch(route('admin.songs.search', { q: query.value }), {
            headers: { Accept: 'application/json' },
        });
        const data = await res.json();
        results.value = data.songs || [];
        highlight.value = results.value.length ? 0 : -1;
    } catch (e) {
        results.value = [];
        highlight.value = -1;
    } finally {
        loading.value = false;
    }
};

const onInput = () => {
    open.value = true;
    clearTimeout(timer);
    timer = setTimeout(fetchSongs, 250);
};

const openDropdown = async () => {
    open.value = true;
    if (! results.value.length) {
        await fetchSongs();
    }
};

const select = (song) => {
    selected.value = song;
    emit('update:modelValue', song.id);
    open.value = false;
    query.value = '';
    results.value = [];
};

const clearSelection = async () => {
    selected.value = null;
    emit('update:modelValue', '');
    open.value = true;
    await nextTick();
    inputEl.value?.focus();
    if (! results.value.length) {
        await fetchSongs();
    }
};

const onKeydown = (e) => {
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        if (! open.value) {
            openDropdown();
            return;
        }
        highlight.value = Math.min(highlight.value + 1, results.value.length - 1);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        highlight.value = Math.max(highlight.value - 1, 0);
    } else if (e.key === 'Enter') {
        e.preventDefault();
        if (open.value && highlight.value >= 0 && results.value[highlight.value]) {
            select(results.value[highlight.value]);
        }
    } else if (e.key === 'Escape') {
        open.value = false;
    }
};

const onClickOutside = (e) => {
    if (root.value && ! root.value.contains(e.target)) {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('mousedown', onClickOutside));
onBeforeUnmount(() => {
    document.removeEventListener('mousedown', onClickOutside);
    clearTimeout(timer);
});
</script>

<template>
    <div ref="root" class="relative">
        <!-- 選択中の楽曲 -->
        <div v-if="selected" class="mt-1 flex items-center gap-3 border border-zinc-700 bg-zinc-900 p-2">
            <div class="h-12 w-12 shrink-0 overflow-hidden bg-zinc-800">
                <img v-if="selected.cover_url" :src="selected.cover_url" class="h-full w-full object-cover" alt="" />
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-zinc-100">{{ selected.title }}</p>
                <p class="truncate text-sm text-zinc-500">
                    {{ selected.artist_name }}<span v-if="selected.genre"> · {{ selected.genre }}</span>
                </p>
            </div>
            <button type="button" class="shrink-0 px-2 text-sm text-brand-400 transition hover:text-brand-300" @click="clearSelection">
                変更
            </button>
        </div>

        <!-- 検索コンボボックス -->
        <div v-else>
            <input
                ref="inputEl"
                v-model="query"
                type="text"
                placeholder="曲名・アーティスト・ジャンルで検索…"
                class="mt-1 w-full border border-zinc-700 bg-zinc-900 px-3 py-2 text-zinc-100 placeholder-zinc-600 focus:border-brand-500 focus:outline-none focus:ring-0"
                role="combobox"
                aria-autocomplete="list"
                :aria-expanded="open"
                autocomplete="off"
                @focus="openDropdown"
                @input="onInput"
                @keydown="onKeydown"
            />
            <div
                v-if="open"
                class="absolute z-20 mt-1 max-h-72 w-full overflow-auto border border-zinc-700 bg-zinc-900 shadow-xl"
            >
                <p v-if="loading" class="px-3 py-3 text-sm text-zinc-500">検索中…</p>
                <p v-else-if="! results.length" class="px-3 py-3 text-sm text-zinc-500">該当する楽曲がありません</p>
                <button
                    v-for="(song, i) in results"
                    :key="song.id"
                    type="button"
                    class="flex w-full items-center gap-3 px-2 py-2 text-left transition"
                    :class="i === highlight ? 'bg-zinc-800' : 'hover:bg-zinc-800/60'"
                    @mousedown.prevent="select(song)"
                    @mousemove="highlight = i"
                >
                    <div class="h-10 w-10 shrink-0 overflow-hidden bg-zinc-800">
                        <img v-if="song.cover_url" :src="song.cover_url" class="h-full w-full object-cover" alt="" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm text-zinc-100">{{ song.title }}</p>
                        <p class="truncate text-xs text-zinc-500">
                            {{ song.artist_name }}<span v-if="song.genre"> · {{ song.genre }}</span>
                        </p>
                    </div>
                </button>
            </div>
        </div>

        <p v-if="error" class="mt-1 text-sm text-red-400">{{ error }}</p>
    </div>
</template>
