<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\ReviewRequest;
use App\Models\Song;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MvpSeeder extends Seeder
{
    public function run(): void
    {
        // --- 編集部 (admin) ---
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => '編集部', 'role' => 'admin', 'handle' => 'editorial', 'password' => 'password', 'bio' => 'SUNO Review 編集部アカウント。']
        );

        // --- レビュワー (招待制) ---
        $reviewers = collect([
            ['name' => 'DJ Aoi', 'handle' => 'aoi', 'email' => 'aoi@example.com', 'bio' => 'クラブ/エレクトロ畑の選曲家。低音とグルーヴに厳しい。'],
            ['name' => '森 健太', 'handle' => 'kenta', 'email' => 'kenta@example.com', 'bio' => '元レコード店員。歌詞と物語性を最重視するタイプ。'],
            ['name' => 'Luna', 'handle' => 'luna', 'email' => 'luna@example.com', 'bio' => 'アンビエント/Lo-fi愛好家。空気感フェチ。'],
        ])->map(fn ($r) => User::firstOrCreate(
            ['email' => $r['email']],
            ['name' => $r['name'], 'handle' => $r['handle'], 'role' => 'reviewer', 'password' => 'password', 'bio' => $r['bio']]
        ))->values();

        // --- 投稿者 (member) ---
        $member = User::firstOrCreate(
            ['email' => 'creator@example.com'],
            ['name' => 'Hiro', 'role' => 'member', 'password' => 'password']
        );

        // --- 楽曲 ---
        $songs = [
            ['title' => 'ネオン東京', 'artist' => 'Hiro', 'genre' => 'City Pop', 'tags' => ['夜', 'ドライブ', 'シティポップ'], 'yt' => true, 'feat' => true, 'views' => 4210, 'desc' => '80年代シティポップへのオマージュ。終電後の都市の孤独と高揚を、きらびやかなシンセで描いた一曲。SUNOには「nostalgic city pop, warm analog synth」と指示した。'],
            ['title' => 'Midnight Lo-fi', 'artist' => 'sleepy cat', 'genre' => 'Lo-fi', 'tags' => ['作業用', 'チル', '睡眠'], 'yt' => false, 'feat' => true, 'views' => 8830, 'desc' => '雨の音をベースにした作業用ビート。眠る前に聴いてほしい。'],
            ['title' => '蒼穹のアリア', 'artist' => 'Mira', 'genre' => 'Ballad', 'tags' => ['泣ける', 'バラード', '感動'], 'yt' => true, 'feat' => false, 'views' => 2980, 'desc' => '大切な人を見送る歌。サビの転調で一気に視界が開ける構成を狙った。'],
            ['title' => 'OVERDRIVE', 'artist' => 'KAITO', 'genre' => 'Rock', 'tags' => ['激しい', 'ギター', '疾走感'], 'yt' => true, 'feat' => false, 'views' => 1560, 'desc' => '衝動をそのまま音にしたギターロック。とにかく爆音で。'],
            ['title' => 'Pulse', 'artist' => 'NOVA', 'genre' => 'EDM', 'tags' => ['クラブ', 'ダンス', 'ビルドアップ'], 'yt' => false, 'feat' => false, 'views' => 3340, 'desc' => 'フロア映えするビッグルーム。ドロップの瞬間のためだけに作った。'],
            ['title' => '木漏れ日', 'artist' => 'Luna', 'genre' => 'Ambient', 'tags' => ['癒し', '集中', '自然'], 'yt' => false, 'feat' => false, 'views' => 990, 'desc' => '森の朝をイメージしたアンビエント。呼吸が深くなる音を探した。'],
            ['title' => 'シティライト・ランデブー', 'artist' => 'Hiro', 'genre' => 'City Pop', 'tags' => ['恋愛', '夜景', 'シティポップ'], 'yt' => true, 'feat' => false, 'views' => 1820, 'desc' => '二人で見た夜景の記憶。AOR寄りのコード感にこだわった。'],
            ['title' => 'リワインド', 'artist' => 'mochi', 'genre' => 'HipHop', 'tags' => ['チル', 'ラップ', '日本語'], 'yt' => true, 'feat' => false, 'views' => 2410, 'desc' => '過ぎた日々を巻き戻すようなチルホップ。リリックは口語にこだわった。'],
        ];

        $created = [];
        foreach ($songs as $i => $s) {
            $created[] = Song::create([
                'submitted_by' => $member->id,
                'title' => $s['title'],
                'artist_name' => $s['artist'],
                'slug' => (Str::slug($s['title']) ?: 'song').'-'.Str::lower(Str::random(6)),
                'suno_url' => 'https://suno.com/song/'.Str::uuid(),
                'youtube_url' => $s['yt'] ? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' : null,
                'genre' => $s['genre'],
                'tags' => $s['tags'],
                'cover_image_path' => "https://picsum.photos/seed/suno-song-{$i}/800/450",
                'description' => $s['desc'],
                'status' => 'published',
                'view_count' => $s['views'],
                'is_featured' => $s['feat'],
                'published_at' => now()->subDays($i),
            ]);
        }

        // --- レビュー (先頭6曲に付与) ---
        $reviewBodies = [
            "最初の8小節で世界が立ち上がる。AIが作ったとは思えない情緒の連続性があって、特にサビ前のブレイクの「間」が絶妙。生成物にありがちな展開の唐突さがない。\n\n惜しいのは2番の歌詞がやや一本調子なところ。だがこの曲が持つ夜の温度はちゃんと本物だ。",
            "作業用として完璧。ループしても飽きない微妙な揺らぎが仕込まれていて、長時間の集中に向く。ミックスも耳に痛くない。\n\nもう一歩踏み込むなら、中盤に一度だけ展開の山がほしかった。",
            "声の表現力に驚いた。サビの伸びと、Aメロの囁くような質感の差で物語が立っている。歌詞の情景描写も具体的で、聴き手の記憶を呼び起こすタイプ。",
            "衝動が真っ直ぐ刺さる。ギターの歪みの選び方が良くて、疾走感が最後まで失速しない。ラウドなのに音像が潰れていないのが好印象。",
            "ドロップの設計が巧い。ビルドアップの緊張からの解放がちゃんと気持ちいい。フロアを想定した低域処理ができている。",
            "静けさのデザインが見事。鳴っていない部分まで含めて音楽になっている。集中したい時の伴走者として長く付き合える一曲。",
        ];
        $reviewTitles = [
            '夜の温度が本物だ', '集中を切らさない設計', '声で物語る一曲',
            '衝動が真っ直ぐ刺さる', 'ドロップのために存在する', '静けさをデザインする',
        ];

        foreach (array_slice($created, 0, 6) as $i => $song) {
            $reviewer = $reviewers[$i % $reviewers->count()];
            Review::create([
                'song_id' => $song->id,
                'reviewer_id' => $reviewer->id,
                'title' => $reviewTitles[$i],
                'slug' => (Str::slug($reviewTitles[$i]) ?: 'review').'-'.Str::lower(Str::random(6)),
                'body' => $reviewBodies[$i],
                'cover_image_path' => "https://picsum.photos/seed/suno-review-{$i}/1200/630",
                'overall_score' => rand(60, 95),
                'published_at' => now()->subDays($i)->addHours(6),
            ]);
        }

        // --- レビュー依頼 (open) ---
        ReviewRequest::create([
            'requester_id' => $member->id,
            'suno_url' => 'https://suno.com/song/'.Str::uuid(),
            'youtube_url' => null,
            'title' => '初めて作ったバラード、率直な感想がほしい',
            'genre' => 'Ballad',
            'note' => 'サビの転調がうまくハマっているか不安です。よろしくお願いします。',
            'status' => 'open',
        ]);
        ReviewRequest::create([
            'requester_id' => $member->id,
            'suno_url' => 'https://suno.com/song/'.Str::uuid(),
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'title' => 'EDMのドロップを評価してほしい',
            'genre' => 'EDM',
            'note' => 'フロアで映えるか、低域の処理が適切かを中心に見てほしいです。',
            'status' => 'open',
        ]);
    }
}
