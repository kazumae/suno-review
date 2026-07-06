<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Song;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        // 公開直後の反映は最大1時間遅れるが、クローラの巡回頻度を考えれば十分
        $xml = Cache::remember('sitemap.xml', now()->addHour(), fn () => $this->build());

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    private function build(): string
    {
        $urls = [];

        foreach (['home', 'landing', 'chart', 'requests.create', 'reviewers.index'] as $name) {
            $urls[] = ['loc' => route($name)];
        }

        foreach (Song::published()->get(['id', 'updated_at']) as $song) {
            $urls[] = ['loc' => route('songs.show', $song), 'lastmod' => $song->updated_at];
        }

        // Reviewのルートキーはslug
        foreach (Review::published()->get(['id', 'slug', 'updated_at']) as $review) {
            $urls[] = ['loc' => route('reviews.show', $review), 'lastmod' => $review->updated_at];
        }

        // 公開レビューを持つレビュワーのみ(一覧ページと同じ条件)
        $reviewers = User::query()
            ->whereIn('role', ['reviewer', 'admin'])
            ->whereNotNull('handle')
            ->whereHas('reviews', fn ($q) => $q->published())
            ->get(['id', 'handle', 'updated_at']);
        foreach ($reviewers as $reviewer) {
            $urls[] = ['loc' => route('reviewers.show', $reviewer->handle), 'lastmod' => $reviewer->updated_at];
        }

        $genres = Song::published()
            ->whereNotNull('genre')
            ->where('genre', '!=', '')
            ->distinct()
            ->pluck('genre');
        foreach ($genres as $genre) {
            $urls[] = ['loc' => route('genres.show', $genre)];
        }

        $body = collect($urls)->map(function (array $url) {
            $lastmod = isset($url['lastmod']) && $url['lastmod']
                ? '<lastmod>'.$url['lastmod']->toAtomString().'</lastmod>'
                : '';

            return '  <url><loc>'.e($url['loc']).'</loc>'.$lastmod.'</url>';
        })->implode("\n");

        return '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"
            .$body."\n"
            .'</urlset>';
    }
}
