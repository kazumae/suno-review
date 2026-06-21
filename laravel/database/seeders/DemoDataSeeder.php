<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\ReviewRequest;
use App\Models\Song;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // --- レビュワー（招待制・重複防止） ---
        $reviewerData = [
            ['name' => 'DJ Aoi', 'handle' => 'aoi', 'email' => 'aoi@example.com', 'bio' => 'クラブ/エレクトロ畑の選曲家。低音とグルーヴに厳しい。'],
            ['name' => '森 健太', 'handle' => 'kenta', 'email' => 'kenta@example.com', 'bio' => '元レコード店員。歌詞と物語性を最重視する。'],
            ['name' => 'Luna', 'handle' => 'luna', 'email' => 'luna@example.com', 'bio' => 'アンビエント/Lo-fi愛好家。空気感フェチ。'],
            ['name' => '橘 さやか', 'handle' => 'sayaka', 'email' => 'sayaka@example.com', 'bio' => 'シティポップとAOR専門。コード感に目がない。'],
            ['name' => 'Tatsuya', 'handle' => 'tatsuya', 'email' => 'tatsuya@example.com', 'bio' => 'ロック/バンドサウンド担当。衝動が好き。'],
            ['name' => 'Mei', 'handle' => 'mei', 'email' => 'mei@example.com', 'bio' => 'バラード/歌モノに強い。声フェチ。'],
        ];
        $reviewers = collect($reviewerData)->map(fn ($r) => User::firstOrCreate(
            ['email' => $r['email']],
            ['name' => $r['name'], 'handle' => $r['handle'], 'role' => 'reviewer', 'password' => 'password', 'bio' => $r['bio']]
        ))->values();

        $member = User::firstOrCreate(
            ['email' => 'creator@example.com'],
            ['name' => 'Hiro', 'role' => 'member', 'password' => 'password']
        );

        $artists = ['Hiro', 'sleepy cat', 'Mira', 'KAITO', 'NOVA', 'Luna', 'mochi', 'Aoba', 'RYU', 'こはく', 'Neon Cat', '藍', 'Sora', 'MIKA', 'daydream', 'Yuki', '蒼', 'rain', 'citrus', '夜長'];
        $genres = ['City Pop', 'Lo-fi', 'Ballad', 'Rock', 'EDM', 'Ambient', 'HipHop', 'J-Pop', 'Jazz', 'Funk', 'Techno', 'Acoustic'];
        $tagPool = ['夜', 'ドライブ', '作業用', 'チル', '睡眠', '泣ける', '感動', '激しい', '疾走感', 'ダンス', 'クラブ', '癒し', '集中', '自然', '恋愛', '切ない', 'エモ', 'レトロ', '未来', '雨', '朝', '都市', '旅', 'ノスタルジー'];

        $nounA = ['真夜中', '雨上がり', '星屑', '深海', 'ネオン', '日曜日', '黄昏', '東京', '真夏', '真冬', '午前4時', '銀河', '水曜日', 'コバルト', '硝子', '週末', '金曜の夜', 'スロウ', 'ラベンダー', '遠回り', 'こぼれた光', 'まばたき'];
        $nounB = ['シグナル', 'パレード', 'ワルツ', 'ブルース', 'プリズム', 'リフレイン', 'メロディー', 'ダイアリー', 'サーキット', 'ロンド', 'スケッチ', 'モノローグ', 'カーニバル', 'ドリーム', 'ノクターン', 'ハイウェイ', 'ステーション', 'エコー'];
        $singles = ['Fade Out', 'Daydream', 'Replay', 'Afterglow', 'Moonlight', 'Citrus', 'Gravity', 'Reflection', 'Velvet', 'Halation', 'Mirage', 'Lullaby'];

        $reviewTitles = ['この一曲は名刺になる', '夜の温度が本物だ', '静けさをデザインする', '衝動が真っ直ぐ刺さる', '声で物語る', '余白の使い方が巧い', '記憶を呼び起こすメロディ', '王道を新しく鳴らす', '作業の友に最適', 'フロアを想定した一曲', '切なさの解像度が高い', '何度も巻き戻したくなる'];
        $bodies = [
            "最初の8小節で世界が立ち上がる。展開の唐突さがなく、情緒の連続性が本物だ。\n\n惜しむらくは2番がやや一本調子。だが核にある温度は確かにある。",
            "作業用として完璧。ループしても飽きない微妙な揺らぎが仕込まれている。ミックスも耳に痛くない。",
            "声の表現力に驚いた。サビの伸びとAメロの囁きの差で物語が立っている。",
            "衝動が真っ直ぐ刺さる。歪みの選び方が良く、疾走感が最後まで失速しない。",
            "ドロップの設計が巧い。ビルドアップの緊張からの解放が気持ちいい。",
            "静けさのデザインが見事。鳴っていない部分まで含めて音楽になっている。",
            "コード進行に色気がある。王道なのにどこか新しい。リハーモナイズの妙。",
            "リズムの粘りが癖になる。タメと突っ込みのバランスが絶妙だ。",
            "歌詞の情景描写が具体的で、聴き手の記憶を呼び起こす。",
            "音数を絞る勇気。引き算の美学がこの曲を品よくしている。",
            "中盤の転調で一気に視界が開ける。構成の組み立てが上手い。",
            "低域の処理が丁寧。小さな音量でも芯が通って聴こえる。",
        ];

        $totalSongs = 45;
        $created = [];
        for ($i = 0; $i < $totalSongs; $i++) {
            $title = $i % 5 === 4
                ? $singles[$i % count($singles)]
                : $nounA[($i * 3) % count($nounA)].'の'.$nounB[($i * 5) % count($nounB)];
            $genre = $genres[$i % count($genres)];

            $created[] = Song::create([
                'submitted_by' => $member->id,
                'title' => $title,
                'artist_name' => $artists[($i * 7) % count($artists)],
                'slug' => (Str::slug($title) ?: 'song').'-'.Str::lower(Str::random(6)),
                'suno_url' => 'https://suno.com/song/'.Str::uuid(),
                'youtube_url' => rand(0, 2) === 0 ? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' : null,
                'genre' => $genre,
                'tags' => collect($tagPool)->shuffle()->take(rand(2, 4))->values()->all(),
                'cover_image_path' => "https://picsum.photos/seed/sunodemo{$i}/800/450",
                'description' => 'SUNOで制作。「'.$title.'」をテーマに、'.$genre.'の質感で書いた一曲。',
                'status' => 'published',
                'view_count' => rand(50, 12000),
                'is_featured' => rand(0, 6) === 0,
                'published_at' => now()->subDays(rand(0, 120))->subHours(rand(0, 23)),
            ]);
        }

        // --- レビュー（曲ごとに0〜3件、注目曲は最低2件） ---
        foreach ($created as $i => $song) {
            $n = rand(0, 3);
            if ($song->is_featured) {
                $n = max($n, 2);
            }
            $used = [];
            for ($j = 0; $j < $n; $j++) {
                $reviewer = $reviewers[($i + $j * 2) % $reviewers->count()];
                if (in_array($reviewer->id, $used, true)) {
                    continue;
                }
                $used[] = $reviewer->id;

                $rt = $reviewTitles[($i + $j) % count($reviewTitles)];

                Review::create([
                    'song_id' => $song->id,
                    'reviewer_id' => $reviewer->id,
                    'title' => $rt,
                    'slug' => (Str::slug($rt) ?: 'review').'-'.Str::lower(Str::random(6)),
                    'body' => $bodies[($i + $j) % count($bodies)],
                    'cover_image_path' => rand(0, 1) ? "https://picsum.photos/seed/sunorev{$i}-{$j}/1200/630" : null,
                    'overall_score' => rand(50, 98),
                    'published_at' => $song->published_at->copy()->addHours(rand(2, 72)),
                ]);
            }
        }

        // --- レビュー依頼（いくつか） ---
        $reqTitles = ['初バラード、率直な感想がほしい', 'EDMのドロップを評価して', 'Lo-fiの空気感どうですか', 'ロックのミックス相談', 'シティポップらしさある？'];
        $reqStatus = ['open', 'open', 'assigned', 'done', 'open'];
        foreach ($reqTitles as $k => $t) {
            ReviewRequest::create([
                'requester_id' => $member->id,
                'suno_url' => 'https://suno.com/song/'.Str::uuid(),
                'title' => $t,
                'genre' => $genres[$k % count($genres)],
                'note' => 'よろしくお願いします。',
                'status' => $reqStatus[$k],
            ]);
        }
    }
}
