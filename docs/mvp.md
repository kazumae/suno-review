# SUNO Song Review Site — MVP 仕様

SUNOで作られた楽曲にレビューと「ストーリー」を加え、発見・拡散させることで **SUNO発のヒット曲** を生み出すキュレーションメディア。

## プロダクトの捉え方

「批評サイト」ではなく **AI曲のローンチ／キュレーションメディア**（note × 音楽キュレーション）。
レビューはヒットを作らない。ヒットを作るのは聴衆と拡散で、レビューはその「きっかけとストーリー」を与える装置。
→ **発見（SEO）と拡散（SNSシェア）** が生命線。だから脱SPA・SSR・直リンク・OGPが必須。

## 決定事項

| 項目 | 決定 |
|------|------|
| 編集スタンス | ハイブリッド（良い曲を選んで載せる＝載ること自体が信号 ＋ 建設的レビュー） |
| 課金 | 当面無料（マネタイズは需要が見えてから）。レビュワーは招待制 |
| スコープ | 薄いMVP |
| スタック | Laravel モノリス + Inertia.js + Vue 3 + SSR |
| インフラ | Docker（nginx + php-fpm 8.4 + MySQL 8 + node） |

### なぜ Inertia + SSR か
「Vueでフロント」と「脱SPA・直リンク・OGP命」を両立させるため。
- ルーティングは Laravel 側＝**全URLが実在**（直リンクOK）
- UIは **Vueコンポーネントで全画面**
- **SSRでHTMLを先に返す**＝OGP/SEOが効く（クローラーはJSを実行しない）
- **モノリス**（API分離不要）

素の Laravel API + Vue SPA は要件「脱SPA」に反するため不採用。

## ロール

- `admin`: 管理・公開可否・レビュワー招待・編集部ピック
- `reviewer`: レビュー執筆（招待制）
- `member`: 楽曲投稿・レビュー依頼（後でいいね/コメント）

## データモデル（MVP）

- **songs**: title / artist_name / suno_url / youtube_url(任意) / genre / tags / cover_image / description（作者のストーリー） / status(pending|published) / submitted_by / published_at
- **reviews**: song_id / reviewer_id / title / body(リッチ) / cover_image（note式） / score_melody / score_lyrics / score_production / score_originality / slug / published_at
- **review_requests**（必須要件）: requester_id / suno_url / youtube_url / note / status(open|assigned|done) / assigned_reviewer_id。依頼時に songs(pending) も生成
- **users**: + role

## ページ / ルート（全て実URL・SSR）

| ルート | 内容 |
|--------|------|
| `/` | トップ: ランキング / 新着 / ジャンル別 |
| `/songs/{id}-{slug}` | 楽曲詳細: SUNO+YouTube埋め込み再生 / 作者のストーリー / レビュー一覧 |
| `/reviews/{slug}` | レビュー記事: カバー画像 / 本文 / スコア（OGPの主役） |
| `/reviewers/{handle}` | レビュワーページ: プロフィール＋レビュー集（tastemaker導線） |
| `/genres/{genre}` | ジャンル別一覧 |
| `/request` | レビュー依頼フォーム（必須要件） |
| `/login` `/register` | 認証 |
| `/admin/*` | 依頼キュー管理・公開/却下・レビュワー招待 |

## ランキング（MVP）

外部再生数は取得不可のため「内部閲覧数 × レビュースコア × 新しさ ＋ 編集部ピック枠（手動）」。
凝った不正対策は後回し。

## OGP / シェア

各曲・各レビューページで `og:image` = **アップロードしたカバー画像**。Inertia SSR の `<Head>` に meta を出力。
動的OG画像合成（タイトル合成カード）は後回し → `spatie/browsershot` で後付け。

## テーマ

Tailwind / 背景黒 + オレンジアクセント（SUNO寄りの暖色オレンジ、hexは実装時に確定）。ダーク基調。

## 後回し（v2以降）

- 外部レビュワーの依頼マーケット化・課金・自動アサイン
- 動的OG画像合成
- ソーシャル（いいね/コメント/フォロー）※レビュワーフォローから優先
- 多言語（JP/EN）※設計だけ意識
- プレイリスト／編集部特集ページ

## 実装状況・運用メモ（バックステージ & ストレージ）

### バックステージ `/admin`（middleware: `auth` + `staff`）
- ロールは admin / reviewer のみアクセス可。reviewer は自分のレビューだけ編集可、レビュワー管理は admin 専用。
- ダッシュボード / 依頼キュー（ステータス更新・「曲にする」で依頼→楽曲化）/ 楽曲CRUD / レビュー執筆（多次元スコア＋カバー）/ レビュワー招待。
- ログイン後は `/admin` へ遷移。一般ユーザーのログイン/新規登録は無し（register無効化済み）。

### ローカル限定クイックログイン
- `app()->environment('local')` のときだけ `/login` にボタン表示＋ `POST /dev/login` を登録。本番では route ごと存在しない＋コントローラでも二重ガード。

### 画像ストレージ（カバー画像）
- アップロードは**デフォルトディスク**に保存し `Storage::url()` でURL解決（accessorは `http` 始まりの外部URLはそのまま返す）。
- ローカル: `FILESYSTEM_DISK=public`（`php artisan storage:link` 済み、`/storage/...` 配信）。
- 本番(S3): `FILESYSTEM_DISK=s3` ＋ `AWS_ACCESS_KEY_ID / AWS_SECRET_ACCESS_KEY / AWS_DEFAULT_REGION / AWS_BUCKET / AWS_URL` を設定するだけ（コード変更不要）。`league/flysystem-aws-s3-v3` 導入済み。
