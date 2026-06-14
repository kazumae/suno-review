<script setup>
import LpLayout from '@/Layouts/LpLayout.vue';
import RequestForm from '@/Components/RequestForm.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="SUNOから、ヒット曲を生みたい">
        <meta name="description" content="AIで作られた曲が市民権を得るには、まず一曲のヒットが要る。SUNOの楽曲に人のレビューとストーリーを載せ、次のヒットを一緒に生み出す。" />
    </Head>

    <LpLayout>
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
                    <a href="#request" class="rounded-full bg-brand-500 px-6 py-3 font-semibold text-black transition hover:bg-brand-400">
                        レビューを依頼する
                    </a>
                </div>
                <div class="mt-12 aspect-[16/7] w-full overflow-hidden ring-1 ring-zinc-800">
                    <img
                        src="/images/lp/hero-01.png"
                        alt="AIが生んだ音楽を、あなたの耳でレビュー。SUNO MUSIC REVIEW MAGAZINE"
                        class="h-full w-full object-cover"
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
                    <div class="aspect-[4/3] w-full overflow-hidden ring-1 ring-zinc-800">
                        <img src="/images/lp/section-02.png" alt="無機質なAIと、感情豊かな人の対比" class="h-full w-full object-cover" />
                    </div>
                </div>
            </div>
        </section>

        <!-- 03 -->
        <section class="border-b border-zinc-800">
            <div class="mx-auto max-w-6xl px-4 py-20">
                <div class="grid items-center gap-10 md:grid-cols-2">
                    <div class="aspect-[4/3] w-full overflow-hidden ring-1 ring-zinc-800 md:order-first">
                        <img src="/images/lp/section-03.png" alt="1枚の楽曲を囲んで語り合い、レビューを書く人々" class="h-full w-full object-cover" />
                    </div>
                    <div>
                        <div class="text-sm font-bold tracking-[0.2em] text-brand-500">03</div>
                        <h2 class="mt-3 text-3xl font-bold leading-tight md:text-4xl">だから、感情とストーリーを載せる。</h2>
                        <p class="mt-5 text-lg text-zinc-300">楽曲を人が聴き、評価し、語る。そこに初めてストーリーが宿る。</p>
                        <p class="mt-4 leading-relaxed text-zinc-400">
                            レビューは点数じゃない。なぜ良いのか、どんな夜に効くのか——ストーリーが、ただの音源を“誰かの一曲”に変える。
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
                    <div class="aspect-[4/3] w-full overflow-hidden ring-1 ring-zinc-800">
                        <img src="/images/lp/section-04.png" alt="無数の楽曲の海で、一枚だけ輝くSUNOの名曲" class="h-full w-full object-cover" />
                    </div>
                </div>
            </div>
        </section>

        <!-- 05 協力 -->
        <section id="request" class="scroll-mt-16 border-b border-zinc-800 bg-zinc-900/30">
            <div class="mx-auto max-w-6xl px-4 py-20">
                <div class="mx-auto max-w-2xl text-center">
                    <div class="text-sm font-bold tracking-[0.2em] text-brand-500">05</div>
                    <h2 class="mt-3 text-3xl font-bold leading-tight md:text-4xl">だから、協力してほしい。</h2>
                    <p class="mt-5 text-lg text-zinc-300">レビューしてほしい曲を、ここから送ってください。編集部とレビュワーが聴いて、ストーリーを添えてレビューします。</p>
                </div>
                <div class="mx-auto mt-10 max-w-2xl">
                    <RequestForm />
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
                    <a href="#request" class="rounded-full bg-brand-500 px-7 py-3.5 font-semibold text-black transition hover:bg-brand-400">
                        レビューを依頼する
                    </a>
                </div>
                <div class="mt-12">
                    <div class="aspect-[16/6] w-full overflow-hidden ring-1 ring-zinc-800">
                        <img src="/images/lp/section-06.png" alt="一緒にSUNOを、AI音楽を盛り上げよう" class="h-full w-full object-cover" />
                    </div>
                </div>
            </div>
        </section>
    </LpLayout>
</template>
