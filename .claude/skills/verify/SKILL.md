---
name: verify
description: SUNO Review をローカルで起動して変更をランタイム検証する手順。docker compose のポート衝突回避・ビルド済みアセットでの駆動・シードデータの在処を含む。
---

# SUNO Review ローカル検証手順

## 起動

ホスト側ポート（8080 / 3306 / 5173）は他プロジェクト（wordpress, lms-environment）と衝突しがち。
override で別ポートに逃がし、必要な3サービスだけ上げるのが確実:

```yaml
# /tmp などに compose.verify.yml を作る
services:
  nginx:
    ports: !override
      - "18080:80"
  mysql:
    ports: !override []   # アプリは内部ネットワークで繋ぐのでホスト公開不要
```

```bash
docker compose -f docker-compose.yml -f <path>/compose.verify.yml up -d app nginx mysql
curl -s -o /dev/null -w "%{http_code}" http://localhost:18080/   # 200 で起動確認
```

- node サービス（vite dev, 5173）は不要。`laravel/public/hot` が無ければ `@vite` はビルド済みアセットを使う。
  フロント変更後は `cd laravel && npm run build`（client + SSR 両方走る）してからブラウザ確認。
- `APP_URL` は 8080 のままでOK。`forceRootUrl` していないので URL 生成はリクエストホスト追従（18080 で正しく動く）。
- SSR サーバを起動しない場合、Inertia はクライアントレンダリングにフォールバックする。

## SSR経路の検証（本番はSSRで動いている）

OGP・JSON-LD など「生HTMLに出るべきもの」は SSR 経路で確認する。ホストの node で SSR サーバを起動し、
アプリコンテナから `host.docker.internal` 経由で繋ぐのが手軽:

```bash
cd laravel && node bootstrap/ssr/ssr.js &          # ホストの13714で起動
printf '\nINERTIA_SSR_URL=http://host.docker.internal:13714\n' >> laravel/.env   # 検証後は必ず削除
curl -s http://localhost:18080/songs/9 | grep -c '<h1'   # 1ならSSRが効いている
```

## データ

- ローカル DB はシード済み。曲 id=9 は youtube_url あり（SUNO/YouTube 両ボタンの検証に使える）。`/songs/9`
- ページデータは Inertia の `data-page` 属性（HTML エスケープされた JSON）から取れる。
- **ルートキーの罠: Song/User はデフォルト(id / handle指定)だが、Review は `getRouteKeyName() = slug`**（URLは `/reviews/{slug}`）。

## 主要フロー

- 楽曲詳細: `/songs/{id}` — SUNOで聴く / YouTube / ShareButtons（X・LINE・Facebook・コピー）
- レビュー依頼（単独ページ）: `/request` = `Pages/Requests/Create.vue`（独自フォーム実装、成功でhomeへリダイレクト）
- レビュー依頼（LP埋め込み）: `/lp` = `Landing.vue` 内の `Components/RequestForm.vue`（`inline: true`、成功でその場に完了表示）
- **注意: 依頼フォームは上記2実装がある。フォーム系の変更は両方に当てること。**
- 送信時は HTML5 バリデーション → 注意モーダル「理解して依頼する」で確定。
- サーババリデーションを通したいときは `suno_url` を256文字超にすると `max:255` で 422 になる。

## GA4 計測の検証

ローカルは `GA4_MEASUREMENT_ID` 未設定で gtag 自体が無い（app.blade.php の条件分岐）。
イベント検証はダミーIDを一時設定して dataLayer を見る:

```bash
printf '\nGA4_MEASUREMENT_ID=G-TESTLOCAL1\n' >> laravel/.env   # 検証後は必ず削除
```

```js
// ブラウザで
window.dataLayer.filter(e => Array.from(e)[0] === 'event').map(e => Array.from(e))
```

カスタムイベント: `share_click {channel}` / `play_click {platform, song_id}` / `request_submit {genre}`（`resources/js/lib/analytics.js` の trackEvent 経由、成功時のみ）。

## 後片付け

- `.env` に足した検証用の行を削除
- 検証で作った依頼レコードを削除:
  `docker compose exec -T app php artisan tinker --execute="App\Models\ReviewRequest::where('suno_url','like','%<マーカー>%')->delete();"`
- 元々コンテナが動いていなかったなら `docker compose down`
