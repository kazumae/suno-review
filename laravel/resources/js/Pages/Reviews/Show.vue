<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import ScorePanel from '@/Components/ScorePanel.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { toJsonLd } from '@/lib/jsonld';

const props = defineProps({
    review: { type: Object, required: true },
});

const paragraphs = (props.review.body || '').split(/\n{2,}/);
const publishedDate = props.review.published_at
    ? new Date(props.review.published_at).toLocaleDateString('ja-JP')
    : '';

// 構造化データ(Review)。SSR時もURLを組めるよう ziggy の base URL を使う
const baseUrl = usePage().props.ziggy.url;

// 動的OGP画像(スコア入り)。?v= は編集時にクローラへ再取得させるキャッシュバスター
const ogImage = `${baseUrl}/og/reviews/${props.review.slug}.png?v=${Date.parse(props.review.updated_at) || 0}`;
const jsonLd = toJsonLd({
    '@context': 'https://schema.org',
    '@type': 'Review',
    name: props.review.title,
    url: `${baseUrl}/reviews/${props.review.slug}`,
    ...(props.review.reviewer?.name ? { author: { '@type': 'Person', name: props.review.reviewer.name } } : {}),
    ...(props.review.published_at ? { datePublished: props.review.published_at } : {}),
    ...(props.review.overall_score != null
        ? { reviewRating: { '@type': 'Rating', ratingValue: props.review.overall_score, bestRating: 100, worstRating: 0 } }
        : {}),
    ...(props.review.song
        ? {
              itemReviewed: {
                  '@type': 'MusicRecording',
                  name: props.review.song.title,
                  url: `${baseUrl}/songs/${props.review.song.id}`,
                  byArtist: { '@type': 'MusicGroup', name: props.review.song.artist_name },
                  ...(props.review.song.cover_url ? { image: props.review.song.cover_url } : {}),
              },
          }
        : {}),
    ...(props.review.body ? { reviewBody: props.review.body.slice(0, 500) } : {}),
});
</script>

<template>
    <Head :title="review.title">
        <meta name="description" :content="(review.body || '').slice(0, 120)" />
        <meta property="og:title" :content="review.title" />
        <meta property="og:description" :content="(review.body || '').slice(0, 120)" />
        <meta property="og:image" :content="ogImage" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:image" :content="ogImage" />
        <component :is="'script'" type="application/ld+json">{{ jsonLd }}</component>
    </Head>

    <PublicLayout>
        <article class="mx-auto max-w-3xl px-4 py-10">
            <!-- Cover -->
            <div v-if="review.cover_url" class="aspect-video w-full overflow-hidden bg-zinc-800 ring-1 ring-zinc-800">
                <img :src="review.cover_url" :alt="review.title" class="h-full w-full object-cover" />
            </div>

            <h1 class="mt-6 text-2xl font-bold md:text-3xl">{{ review.title }}</h1>

            <!-- Byline -->
            <div class="mt-4 flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden bg-zinc-800 text-sm font-bold text-zinc-500">
                    <img v-if="review.reviewer?.avatar_url" :src="review.reviewer.avatar_url" :alt="review.reviewer?.name" class="h-full w-full object-cover" />
                    <span v-else>{{ review.reviewer?.name?.charAt(0) }}</span>
                </div>
                <div class="text-sm">
                    <Link v-if="review.reviewer?.handle" :href="route('reviewers.show', review.reviewer.handle)" class="font-semibold transition hover:text-brand-400">
                        {{ review.reviewer?.name }}
                    </Link>
                    <div class="text-zinc-500">{{ publishedDate }}</div>
                </div>
            </div>

            <!-- Reviewed song -->
            <Link
                v-if="review.song"
                :href="route('songs.show', review.song.id)"
                class="mt-6 flex items-center gap-4 bg-zinc-900 p-3 ring-1 ring-zinc-800 transition hover:ring-brand-500/60"
            >
                <div class="h-16 w-28 shrink-0 overflow-hidden bg-zinc-800">
                    <img v-if="review.song.cover_url" :src="review.song.cover_url" class="h-full w-full object-cover" />
                </div>
                <div class="min-w-0">
                    <div class="text-xs text-zinc-500">レビュー対象</div>
                    <div class="truncate font-semibold">{{ review.song.title }}</div>
                    <div class="truncate text-sm text-zinc-400">{{ review.song.artist_name }}</div>
                </div>
            </Link>

            <!-- Scores -->
            <div class="mt-6">
                <ScorePanel :review="review" />
            </div>

            <!-- Body -->
            <div class="mt-8 space-y-4 text-lg leading-relaxed text-zinc-200">
                <p v-for="(p, i) in paragraphs" :key="i" class="whitespace-pre-line">{{ p }}</p>
            </div>
        </article>
    </PublicLayout>
</template>
