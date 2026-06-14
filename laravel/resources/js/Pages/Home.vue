<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SongCard from '@/Components/SongCard.vue';
import FeatureTile from '@/Components/FeatureTile.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    featured: { type: Array, default: () => [] },
    ranking: { type: Array, default: () => [] },
    latest: { type: Array, default: () => [] },
    genres: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="SUNOの楽曲レビュー">
        <meta name="description" content="SUNOで作られた楽曲をレビュワーが聴き込み、ストーリーを添える。ランキング・新着・ジャンルから次のヒットを見つけよう。" />
    </Head>

    <PublicLayout>
        <!-- Main visual: mosaic -->
        <section v-if="featured.length" class="border-b border-zinc-800">
            <div class="mx-auto max-w-7xl px-4 py-6">
                <div class="grid grid-cols-1 gap-0 md:h-[60vh] md:max-h-[600px] md:min-h-[440px] md:grid-cols-3 md:grid-rows-2">
                    <FeatureTile v-if="featured[0]" :song="featured[0]" large class="md:col-span-1 md:row-span-2" />
                    <FeatureTile v-if="featured[1]" :song="featured[1]" class="md:col-span-2 md:row-span-1" />
                    <FeatureTile v-if="featured[2]" :song="featured[2]" class="md:col-span-1 md:row-span-1" />
                    <FeatureTile v-if="featured[3]" :song="featured[3]" class="md:col-span-1 md:row-span-1" />
                </div>
            </div>
        </section>

        <div class="mx-auto max-w-7xl space-y-12 px-4 py-10">
            <!-- Ranking: compact chart list -->
            <section v-if="ranking.length">
                <h2 class="mb-4 flex items-center gap-3 text-xl font-bold">
                    <span class="inline-block h-5 w-1 bg-brand-500"></span> ランキング
                </h2>
                <div class="grid gap-2 sm:grid-cols-2">
                    <Link
                        v-for="(song, i) in ranking"
                        :key="song.id"
                        :href="route('songs.show', song.id)"
                        class="group flex items-center gap-3 bg-zinc-900 p-2 ring-1 ring-zinc-800 transition hover:ring-brand-500/60"
                    >
                        <span class="w-6 shrink-0 text-center text-lg font-bold text-brand-500">{{ i + 1 }}</span>
                        <div class="h-12 w-20 shrink-0 overflow-hidden bg-zinc-800">
                            <img v-if="song.cover_url" :src="song.cover_url" :alt="song.title" class="h-full w-full object-cover" loading="lazy" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="truncate font-semibold transition group-hover:text-brand-400">{{ song.title }}</div>
                            <div class="truncate text-xs text-zinc-400">{{ song.artist_name }}</div>
                        </div>
                        <span
                            v-if="song.reviews_avg_overall_score"
                            class="shrink-0 border border-brand-500/50 px-1.5 py-0.5 text-xs font-semibold text-brand-400"
                        >
                            {{ Number(song.reviews_avg_overall_score).toFixed(1) }}
                        </span>
                    </Link>
                </div>
            </section>

            <!-- New arrivals: dense magazine grid -->
            <section v-if="latest.length">
                <h2 class="mb-4 flex items-center gap-3 text-xl font-bold">
                    <span class="inline-block h-5 w-1 bg-brand-500"></span> 新着
                </h2>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                    <SongCard v-for="song in latest" :key="song.id" :song="song" />
                </div>
            </section>

            <!-- By genre -->
            <section id="genres" class="scroll-mt-20 space-y-10">
                <div v-for="g in genres" :key="g.name">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="flex items-center gap-3 text-xl font-bold">
                            <span class="inline-block h-5 w-1 bg-brand-500"></span> {{ g.name }}
                        </h2>
                        <Link :href="route('genres.show', g.name)" class="text-sm text-brand-400 transition hover:text-brand-300">
                            もっと見る →
                        </Link>
                    </div>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                        <SongCard v-for="song in g.songs" :key="song.id" :song="song" />
                    </div>
                </div>
            </section>
        </div>
    </PublicLayout>
</template>
