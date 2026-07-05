// GA4 カスタムイベント送信ヘルパー。
// gtag は GA4_MEASUREMENT_ID 設定時のみ app.blade.php で定義されるため、
// 未設定の環境（ローカル等）や SSR では何もせず握りつぶす。
export function trackEvent(name, params = {}) {
    if (typeof window !== 'undefined' && typeof window.gtag === 'function') {
        window.gtag('event', name, params);
    }
}
