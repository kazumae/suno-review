<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class ChartController extends Controller
{
    private const SIZE = 20;

    public function index(): Response
    {
        $songs = Cache::remember('chart.v1', now()->addMinutes(30), fn () => $this->ranking());

        return Inertia::render('Chart/Index', [
            'songs' => $songs,
        ]);
    }

    /**
     * mvp.md既定の式「内部閲覧数 × レビュースコア × 新しさ ＋ 編集部ピック枠」の先行版。
     * view_count は累積値しか無いため正確な週間チャートはまだ出せない。
     * 期間集計(日次スナップショット)は Phase 2 のリアクション導入とあわせて精度UPする。
     */
    /**
     * @return array<int, array<string, mixed>>
     */
    private function ranking(): array
    {
        // モデルのままキャッシュするとドライバ次第で壊れる(__PHP_Incomplete_Class)ため、plain arrayで持つ
        return Song::published()
            ->withCount(['reviews' => fn ($q) => $q->published()])
            ->withAvg(['reviews' => fn ($q) => $q->published()], 'overall_score')
            ->get()
            ->map(function (Song $song) {
                $song->setAttribute('chart_score', round($this->score($song), 3));

                return $song;
            })
            ->sortByDesc('chart_score')
            ->take(self::SIZE)
            ->values()
            ->toArray();
    }

    private function score(Song $song): float
    {
        // 閲覧数: 対数で減衰させ、桁違いの曲がチャート全体を支配しないようにする
        $views = log10($song->view_count + 1) + 1;

        // レビュースコア: 未レビュー曲は中立の50扱いで露出の芽を残す。0点レビューは正直に沈む
        $score = ($song->reviews_avg_overall_score ?? 50) / 100;

        // 新しさ: 公開から30日で1/e に減衰。「いま」の注目度を優先する
        $days = ($song->published_at ?? $song->created_at)->diffInDays(now());
        $freshness = exp(-$days / 30);

        // 編集部ピック: 加点で浮かせる(絶対固定はしない)
        $editorial = $song->featured_position !== null ? 1.5 : 0;

        return $views * $score * $freshness + $editorial;
    }
}
