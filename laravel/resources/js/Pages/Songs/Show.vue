<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import ShareButtons from '@/Components/ShareButtons.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    song: { type: Object, required: true },
});
</script>

<template>
    <Head :title="song.title">
        <meta name="description" :content="(song.description || '').slice(0, 120)" />
        <meta property="og:title" :content="song.title + ' / ' + song.artist_name" />
        <meta property="og:description" :content="(song.description || '').slice(0, 120)" />
        <meta v-if="song.cover_url" property="og:image" :content="song.cover_url" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta v-if="song.cover_url" name="twitter:image" :content="song.cover_url" />
    </Head>

    <PublicLayout>
        <div class="mx-auto max-w-4xl px-4 py-10">
            <!-- Header -->
            <div class="flex flex-col gap-6 md:flex-row">
                <div class="w-full shrink-0 md:w-72">
                    <div class="aspect-video w-full overflow-hidden bg-zinc-800 ring-1 ring-zinc-800">
                        <img v-if="song.cover_url" :src="song.cover_url" :alt="song.title" class="h-full w-full object-cover" />
                    </div>
                </div>
                <div class="min-w-0">
                    <div v-if="song.genre" class="mb-2">
                        <Link :href="route('genres.show', song.genre)" class="bg-zinc-800 px-2 py-0.5 text-xs text-zinc-300 transition hover:text-white">
                            {{ song.genre }}
                        </Link>
                    </div>
                    <h1 class="text-2xl font-bold md:text-3xl">{{ song.title }}</h1>
                    <p class="mt-1 text-zinc-400">
                        <a
                            v-if="song.artist_url"
                            :href="song.artist_url"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex items-center gap-1 transition hover:text-brand-400"
                        >
                            {{ song.artist_name }}
                            <span aria-hidden="true" class="text-xs">↗</span>
                        </a>
                        <span v-else>{{ song.artist_name }}</span>
                    </p>
                    <div class="mt-4 flex flex-wrap gap-2 text-sm">
                        <a :href="song.suno_url" target="_blank" rel="noopener" class="rounded-full bg-brand-500 px-4 py-2 font-semibold text-black transition hover:bg-brand-400">SUNOで聴く</a>
                        <a v-if="song.youtube_url" :href="song.youtube_url" target="_blank" rel="noopener" class="rounded-full border border-zinc-700 px-4 py-2 text-zinc-200 transition hover:border-zinc-500">YouTube</a>
                    </div>
                    <p class="mt-3 text-xs text-zinc-600">{{ song.view_count }} 回再生 ・ レビュー {{ song.reviews?.length || 0 }}件</p>
                    <div class="mt-4">
                        <ShareButtons :url="route('songs.show', song.id)" :title="`${song.title} / ${song.artist_name}`" />
                    </div>
                </div>
            </div>

            <!-- Players -->
            <div class="mt-8 space-y-4">
                <div v-if="song.suno_embed_url" class="overflow-hidden ring-1 ring-zinc-800">
                    <iframe :src="song.suno_embed_url" class="h-40 w-full" allow="autoplay" loading="lazy"></iframe>
                </div>
                <div v-if="song.youtube_embed_url" class="aspect-video overflow-hidden ring-1 ring-zinc-800">
                    <iframe :src="song.youtube_embed_url" class="h-full w-full" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>

            <!-- Context -->
            <section v-if="song.description" class="mt-10">
                <h2 class="mb-3 flex items-center gap-3 text-lg font-bold">
                    <span class="inline-block h-5 w-1 bg-brand-500"></span> 曲紹介
                </h2>
                <p class="whitespace-pre-line leading-relaxed text-zinc-300">{{ song.description }}</p>
                <p v-if="song.submitter" class="mt-3 text-sm text-zinc-500">投稿: {{ song.submitter.name }}</p>
            </section>

            <!-- Reviews -->
            <section class="mt-12">
                <h2 class="mb-5 flex items-center gap-3 text-lg font-bold">
                    <span class="inline-block h-5 w-1 bg-brand-500"></span> レビュー
                </h2>
                <div v-if="song.reviews?.length" class="space-y-5">
                    <article v-for="review in song.reviews" :key="review.id" class="bg-zinc-900 p-5 ring-1 ring-zinc-800">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 overflow-hidden bg-zinc-800">
                                <img v-if="review.reviewer?.avatar_path" :src="review.reviewer.avatar_path" class="h-full w-full object-cover" />
                            </div>
                            <div class="text-sm">
                                <Link v-if="review.reviewer?.handle" :href="route('reviewers.show', review.reviewer.handle)" class="font-semibold transition hover:text-brand-400">
                                    {{ review.reviewer?.name }}
                                </Link>
                                <span v-else class="font-semibold">{{ review.reviewer?.name }}</span>
                                <span class="ml-2 text-brand-400">{{ Number(review.overall_score).toFixed(1) }}</span>
                            </div>
                        </div>
                        <h3 class="mt-3 text-lg font-bold">
                            <Link :href="route('reviews.show', review.slug)" class="transition hover:text-brand-400">{{ review.title }}</Link>
                        </h3>
                        <p class="mt-2 line-clamp-3 whitespace-pre-line text-zinc-400">{{ review.body }}</p>
                        <Link :href="route('reviews.show', review.slug)" class="mt-3 inline-block text-sm text-brand-400 transition hover:text-brand-300">レビューを読む →</Link>
                    </article>
                </div>
                <p v-else class="text-zinc-500">まだレビューはありません。最初のレビューを待っています。</p>
            </section>
        </div>
    </PublicLayout>
</template>
