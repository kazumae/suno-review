<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import ImageSlot from '@/Components/ImageSlot.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="SUNOから、ヒット曲を生みたい">
        <meta name="description" content="AIで作られた曲が市民権を得るには、まず一曲のヒットが要る。SUNOの楽曲に人のレビューと文脈を載せ、次のヒットを一緒に生み出す。" />
    </Head>

    <PublicLayout>
        <!-- 01 HERO -->
        <section class="border-b border-zinc-800 bg-gradient-to-b from-zinc-900 to-zinc-950">
            <div class="mx-auto max-w-6xl px-4 py-20 md:py-28">
                <div class="text-sm font-bold tracking-[0.2em] text-brand-500">01</div>
                <h1 class="mt-3 text-4xl font-bold leading-tight tracking-tight md:text-6xl">
                    SUNOから、<br class="hidden sm:block" /><span class="text-brand-500">ヒット曲</span>を生みたい。
                </h1>
                <p class="mt-6 max-w-2xl text-lg text-zinc-300 md:text-xl">
                    AIで作られた曲が市民権を得るには、まず一曲、世の中に届く「ヒット」が要る。
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <Link :href="route('requests.create')" class="rounded-full bg-brand-500 px-6 py-3 font-semibold text-black transition hover:bg-brand-400">
                        レビューを依頼する
                    </Link>
                    <Link :href="route('home')" class="rounded-full border border-zinc-700 px-6 py-3 font-semibold text-zinc-200 transition hover:border-zinc-500">
                        楽曲を見る
                    </Link>
                </div>
                <div class="mt-12">
                    <ImageSlot
                        ratio="aspect-[16/7]"
                        size="2400×1050px（16:7 / 横長ヒーロー）"
                        caption="ヒーロー画像案: 満員のライブ会場で観客が熱狂し、ステージにオレンジのスポットライト。AI生成の楽曲が大舞台で鳴り響いているイメージ（黒基調＋オレンジの光）。"
                    />
                </div>
            </div>
        </section>

        <!-- 02 -->
        <section class="border-b border-zinc-800">
            <div class="mx-auto max-w-6xl px-4 py-20">
                <div class="grid items-center gap-10 md:grid-cols-2">
                    <div>
                        <div class="text-sm font-bold tracking-[0.2em] text-brand-500">02</div>
                        <h2 class="mt-3 text-3xl font-bold leading-tight md:text-4xl">人は、AIに興味を持たない。</h2>
                        <p class="mt-5 text-lg text-zinc-300">人が惹かれるのは、いつだって人。たとえそれが不完全でも。</p>
                        <p class="mt-4 leading-relaxed text-zinc-400">
                            「AIが作りました」だけでは、心は動かない。技術の話の先にある“誰かの想い”にこそ、人は反応する。
                        </p>
                    </div>
                    <ImageSlot
                        size="1200×900px（4:3）"
                        caption="画像案: 無機質なAI／ロボットの図と、感情豊かな人の横顔の対比。人の側にだけ温かいオレンジの光が当たっている。"
                    />
                </div>
            </div>
        </section>

        <!-- 03 -->
        <section class="border-b border-zinc-800">
            <div class="mx-auto max-w-6xl px-4 py-20">
                <div class="grid items-center gap-10 md:grid-cols-2">
                    <ImageSlot
                        class="md:order-first"
                        size="1200×900px（4:3）"
                        caption="画像案: 1枚のジャケットを囲んで何人かが熱心に語り合い・レビューを書いている。吹き出し、星評価、手書きのメモ。"
                    />
                    <div>
                        <div class="text-sm font-bold tracking-[0.2em] text-brand-500">03</div>
                        <h2 class="mt-3 text-3xl font-bold leading-tight md:text-4xl">だから、感情とストーリーを載せる。</h2>
                        <p class="mt-5 text-lg text-zinc-300">楽曲を人が聴き、評価し、語る。そこに初めてストーリーが宿る。</p>
                        <p class="mt-4 leading-relaxed text-zinc-400">
                            レビューは点数じゃない。なぜ良いのか、どんな夜に効くのか——文脈が、ただの音源を“誰かの一曲”に変える。
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 04 -->
        <section class="border-b border-zinc-800">
            <div class="mx-auto max-w-6xl px-4 py-20">
                <div class="grid items-center gap-10 md:grid-cols-2">
                    <div>
                        <div class="text-sm font-bold tracking-[0.2em] text-brand-500">04</div>
                        <h2 class="mt-3 text-3xl font-bold leading-tight md:text-4xl">SUNOの楽曲は、膨大すぎる。</h2>
                        <p class="mt-5 text-lg text-zinc-300">毎日、無数の曲が生まれ続ける。その海から名曲を個人で探し当てるのは至難の業。</p>
                        <p class="mt-4 leading-relaxed text-zinc-400">
                            埋もれた一曲を見つけ、光を当てる。それは一人より、みんなでやったほうがずっと早い。
                        </p>
                    </div>
                    <ImageSlot
                        size="1200×900px（4:3）"
                        caption="画像案: 無数の楽曲ジャケットが敷き詰められた巨大な壁／海。その中の一枚だけがオレンジに発光している（＝埋もれた名曲）。"
                    />
                </div>
            </div>
        </section>

        <!-- 05 協力 -->
        <section class="border-b border-zinc-800 bg-zinc-900/30">
            <div class="mx-auto max-w-6xl px-4 py-20">
                <div class="text-sm font-bold tracking-[0.2em] text-brand-500">05</div>
                <h2 class="mt-3 text-3xl font-bold leading-tight md:text-4xl">だから、協力してほしい。</h2>
                <p class="mt-5 max-w-2xl text-lg text-zinc-300">レビューしてほしい曲がある人も、曲を聴いて語りたい人も。</p>

                <div class="mt-10 grid gap-4 md:grid-cols-2">
                    <div class="border border-zinc-800 bg-zinc-950 p-8">
                        <h3 class="text-xl font-bold">曲を届けたい人へ</h3>
                        <p class="mt-3 leading-relaxed text-zinc-400">
                            自信作をレビュー依頼。第三者の言葉と文脈を添えて、ただのアップロードを“作品”として世に出す。
                        </p>
                        <Link :href="route('requests.create')" class="mt-5 inline-block rounded-full bg-brand-500 px-5 py-2.5 text-sm font-semibold text-black transition hover:bg-brand-400">
                            レビューを依頼する
                        </Link>
                    </div>
                    <div class="border border-zinc-800 bg-zinc-950 p-8">
                        <h3 class="text-xl font-bold">聴いて語りたい人へ</h3>
                        <p class="mt-3 leading-relaxed text-zinc-400">
                            目利きとして、曲にストーリーを与える側に。あなたのレビューが、次のヒットの最初の一押しになる。
                        </p>
                        <a href="mailto:kazu.maekawa@gmail.com?subject=SUNO Review レビュワー応募" class="mt-5 inline-block rounded-full border border-zinc-700 px-5 py-2.5 text-sm font-semibold text-zinc-200 transition hover:border-brand-500 hover:text-brand-400">
                            レビュワーに応募する
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 06 FINAL CTA -->
        <section class="relative overflow-hidden">
            <div class="mx-auto max-w-4xl px-4 py-24 text-center">
                <div class="text-sm font-bold tracking-[0.2em] text-brand-500">06</div>
                <h2 class="mt-4 text-3xl font-bold leading-tight md:text-5xl">
                    一緒に、SUNOを。<br />AI音楽を、盛り上げよう。
                </h2>
                <p class="mt-6 text-lg text-zinc-300">次のヒットは、ここから生まれる。</p>
                <div class="mt-9 flex flex-wrap justify-center gap-3">
                    <Link :href="route('requests.create')" class="rounded-full bg-brand-500 px-7 py-3.5 font-semibold text-black transition hover:bg-brand-400">
                        レビューを依頼する
                    </Link>
                    <Link :href="route('home')" class="rounded-full border border-zinc-700 px-7 py-3.5 font-semibold text-zinc-200 transition hover:border-zinc-500">
                        楽曲を見る
                    </Link>
                </div>
                <div class="mt-12">
                    <ImageSlot
                        ratio="aspect-[16/6]"
                        size="1920×720px（16:6 / 横長）"
                        caption="フッター画像案: 多様な人が肩を組む／手を重ねる連帯のイメージ。背景に音の波形とステージ照明、オレンジ基調。"
                    />
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
