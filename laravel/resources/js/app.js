import '../css/app.css';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// GTM(dataLayer) へ SPA 遷移ごとのページビューを送る。
// 素のGTMスニペットは初回ロードしか拾えず、Inertia の SPA 遷移を取りこぼすため手動送信する。
// Inertia の navigate は初回ロードでも発火するので、初回ぶんは GTM の GA4設定タグに任せ、
// ここでは2回目以降（SPA遷移・戻る/進む）のみ送って二重計測を防ぐ。
// createInertiaApp より前に登録し、初回 navigate を確実に捕捉してスキップする。
let skipInitialNavigate = true;
router.on('navigate', () => {
    if (skipInitialNavigate) {
        skipInitialNavigate = false;
        return;
    }
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
        event: 'pageview',
        page_path: window.location.pathname + window.location.search,
        page_location: window.location.href,
        page_title: document.title,
    });
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
