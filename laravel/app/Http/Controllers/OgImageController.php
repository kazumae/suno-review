<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Song;
use App\Services\OgImageRenderer;
use Closure;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OgImageController extends Controller
{
    public function song(Song $song, OgImageRenderer $renderer): BinaryFileResponse
    {
        abort_unless($song->status === 'published', 404);

        $avg = $song->reviews()->published()->avg('overall_score');
        $avg = $avg !== null ? (int) round($avg) : null;

        $hash = md5(implode('|', [
            OgImageRenderer::VERSION,
            $song->title, $song->artist_name, (string) $song->genre,
            (string) $avg, (string) $song->cover_image_path, (string) $song->updated_at,
        ]));

        $path = $this->cached('songs', $song->id, $hash, fn () => $renderer->song($song, $avg));

        return $this->respond($path);
    }

    public function review(Review $review, OgImageRenderer $renderer): BinaryFileResponse
    {
        abort_if($review->published_at === null, 404);

        $review->load(['song', 'reviewer:id,name']);

        $hash = md5(implode('|', [
            OgImageRenderer::VERSION,
            $review->title, (string) $review->overall_score, (string) $review->reviewer?->name,
            (string) $review->song?->title, (string) $review->song?->artist_name,
            (string) ($review->song?->cover_image_path ?? $review->cover_image_path), (string) $review->updated_at,
        ]));

        $path = $this->cached('reviews', $review->id, $hash, fn () => $renderer->review($review));

        return $this->respond($path);
    }

    /**
     * 生成結果をstorage/app/ogにキャッシュする。内容が変わればhashが変わり、旧世代は掃除。
     * og:image URL側の ?v=updated_at がクローラのキャッシュバスターになる。
     */
    private function cached(string $dir, int $id, string $hash, Closure $render): string
    {
        $file = storage_path("app/og/{$dir}/{$id}-{$hash}.png");

        if (! is_file($file)) {
            File::ensureDirectoryExists(dirname($file));
            foreach (glob(storage_path("app/og/{$dir}/{$id}-")."*.png") as $old) {
                @unlink($old);
            }
            file_put_contents($file, $render());
        }

        return $file;
    }

    private function respond(string $path): BinaryFileResponse
    {
        return response()->file($path, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
