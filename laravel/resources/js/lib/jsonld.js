// JSON-LD(構造化データ)を <script type="application/ld+json"> に埋めるためのシリアライズ。
// タイトル等に "</script>" が含まれるとタグを突き抜けてXSSになるため、< をエスケープする。
export function toJsonLd(schema) {
    return JSON.stringify(schema).replace(/</g, '\\u003c');
}
