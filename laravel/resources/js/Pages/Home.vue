<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SongCard from '@/Components/SongCard.vue';
import FeatureTile from '@/Components/FeatureTile.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    featured: { type: Array, default: () => [] },
    latestReviews: { type: Array, default: () => [] },
    latest: { type: Array, default: () => [] },
    genres: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="SUNOの楽曲レビュー">
        <meta name="description" content="SUNOで作られた楽曲をレビュワーが聴き込み、ストーリーを添える。新着レビュー・ジャンルから次のヒットを見つけよう。" />
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
            <!-- Latest reviews -->
            <section v-if="latestReviews.length">
                <h2 class="mb-4 flex items-center gap-3 text-xl font-bold">
                    <span class="inline-block h-5 w-1 bg-brand-500"></span> 新着レビュー
                </h2>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                    <Link
                        v-for="review in latestReviews"
                        :key="review.id"
                        :href="route('reviews.show', review.slug)"
                        class="group block"
                    >
                        <div class="overflow-hidden bg-zinc-900 ring-1 ring-zinc-800 transition group-hover:ring-brand-500/60">
                            <div class="aspect-video w-full overflow-hidden bg-zinc-800">
                                <img
                                    v-if="review.cover_url || review.song?.cover_url"
                                    :src="review.cover_url || review.song.cover_url"
                                    :alt="review.title"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                    loading="lazy"
                                />
                            </div>
                            <div class="p-3">
                                <h3 class="truncate font-semibold text-zinc-100 transition group-hover:text-brand-400">{{ review.title }}</h3>
                                <p class="truncate text-xs text-zinc-400">{{ review.song?.title }}</p>
                                <div class="mt-2 flex items-center justify-between gap-2">
                                    <div class="flex min-w-0 items-center gap-1.5">
                                        <img
                                            v-if="review.reviewer?.avatar_url"
                                            :src="review.reviewer.avatar_url"
                                            :alt="review.reviewer.name"
                                            class="h-4 w-4 shrink-0 object-cover"
                                        />
                                        <span class="truncate text-xs text-zinc-500">{{ review.reviewer?.name }}</span>
                                    </div>
                                    <span
                                        v-if="review.overall_score"
                                        class="shrink-0 border border-brand-500/50 px-1.5 py-0.5 text-xs font-semibold text-brand-400"
                                    >
                                        {{ review.overall_score }}
                                    </span>
                                </div>
                            </div>
                        </div>
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
