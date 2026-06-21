<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        // ランキング: 編集部ピック → レビュー平均点 → 閲覧数
        $ranking = Song::published()
            ->withCount(['reviews' => fn ($q) => $q->published()])
            ->withAvg(['reviews' => fn ($q) => $q->published()], 'overall_score')
            ->orderByDesc('is_featured')
            ->orderByDesc('reviews_avg_overall_score')
            ->orderByDesc('view_count')
            ->take(8)
            ->get();

        // 新着
        $latest = Song::published()
            ->withCount(['reviews' => fn ($q) => $q->published()])
            ->latest('published_at')
            ->take(8)
            ->get();

        // ジャンル別
        $genres = Song::published()
            ->withCount(['reviews' => fn ($q) => $q->published()])
            ->latest('published_at')
            ->get()
            ->groupBy('genre')
            ->map(fn ($songs, $genre) => [
                'name' => $genre,
                'songs' => $songs->take(4)->values(),
            ])
            ->values();

        // メインビジュアル: 編集部が指定したスロット(1-4)を優先、空きはランキング上位で補完
        $curated = Song::published()
            ->whereNotNull('featured_position')
            ->withCount(['reviews' => fn ($q) => $q->published()])
            ->withAvg(['reviews' => fn ($q) => $q->published()], 'overall_score')
            ->orderBy('featured_position')
            ->get()
            ->keyBy('featured_position');

        $usedIds = $curated->pluck('id')->all();
        $fillPool = $ranking->reject(fn ($s) => in_array($s->id, $usedIds, true))->values();

        $featured = collect();
        $fillIndex = 0;
        for ($pos = 1; $pos <= 4; $pos++) {
            if ($curated->has($pos)) {
                $featured->push($curated->get($pos));
            } elseif (isset($fillPool[$fillIndex])) {
                $featured->push($fillPool[$fillIndex]);
                $fillIndex++;
            }
        }

        return Inertia::render('Home', [
            'featured' => $featured->values(),
            'ranking' => $ranking,
            'latest' => $latest,
            'genres' => $genres,
        ]);
    }
}
