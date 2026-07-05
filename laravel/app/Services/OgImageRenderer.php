<?php

namespace App\Services;

use App\Models\Review;
use App\Models\Song;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * OGP画像(1200x630 PNG)を素のGDで合成する。
 * 方針: 依存を増やさない(browsershot/Chromeはmicroインスタンスに重すぎ、Intervention Imageも固定レイアウト1枚には過剰)。
 * デザイン: カバー全面 + 黒グラデーションオーバーレイ + 白テキスト + ブランドオレンジ。角丸・絵文字なし。
 */
class OgImageRenderer
{
    /** デザイン変更時にインクリメントする。キャッシュキーに混ぜて旧デザインの生成済み画像を無効化する */
    public const VERSION = 1;

    private const W = 1200;

    private const H = 630;

    private const MARGIN = 72;

    private const ORANGE = [0xf9, 0x73, 0x16]; // brand-500

    private const WHITE = [0xff, 0xff, 0xff];

    private const LIGHT = [0xd4, 0xd4, 0xd8];  // zinc-300 写真の上でも読める明るさ

    private const GRAY = [0xa1, 0xa1, 0xaa];   // zinc-400

    private const GRAY_DIM = [0x8a, 0x8a, 0x93];

    public function song(Song $song, ?int $avgScore): string
    {
        $img = $this->canvas($song->cover_image_path);

        $this->wordmark($img);

        $x = self::MARGIN;
        $y = 200;

        if ($song->genre) {
            $this->text($img, mb_strtoupper($song->genre), 26, $x, $y, self::ORANGE);
            $y += 58;
        }

        // スコアを右下に出す場合はタイトルの折返し幅を狭めて衝突を避ける
        $titleWidth = $avgScore !== null ? 760 : self::W - self::MARGIN * 2;
        $y = $this->paragraph($img, $song->title, 62, $x, $y, $titleWidth, 3, self::WHITE, 80);

        $this->text($img, $song->artist_name, 34, $x, $y + 70, self::LIGHT);

        if ($avgScore !== null) {
            $this->score($img, $avgScore);
        }

        $this->footer($img);

        return $this->png($img);
    }

    public function review(Review $review): string
    {
        $song = $review->song;
        $img = $this->canvas($song?->cover_image_path ?? $review->cover_image_path);

        $this->wordmark($img);

        $x = self::MARGIN;
        $y = 200;

        $y = $this->paragraph($img, $review->title, 54, $x, $y, 740, 3, self::WHITE, 72);

        if ($song) {
            $this->text($img, "{$song->title} / {$song->artist_name}", 28, $x, $y + 62, self::LIGHT);
        }

        if ($review->reviewer?->name) {
            $this->text($img, "Reviewed by {$review->reviewer->name}", 24, $x, self::H - 64, self::GRAY);
        }

        $this->score($img, $review->overall_score);

        $this->footer($img);

        return $this->png($img);
    }

    /**
     * カバー画像を敷いた土台を作る。カバーが無い/読めないときは無地の黒。
     */
    private function canvas(?string $coverPath)
    {
        $img = imagecreatetruecolor(self::W, self::H);
        imagefill($img, 0, 0, $this->color($img, [0x09, 0x09, 0x0b])); // zinc-950

        if ($cover = $this->loadCover($coverPath)) {
            $this->drawCoverFilled($img, $cover);
            imagedestroy($cover);
        }

        // 左が濃く右に薄まる黒グラデーション。派手なカバーでもテキストが必ず勝つ濃さにする
        for ($col = 0; $col < self::W; $col++) {
            $alpha = (int) round(8 + (38 - 8) * ($col / self::W)); // 0=不透明, 127=透明
            $line = imagecolorallocatealpha($img, 0x09, 0x09, 0x0b, $alpha);
            imageline($img, $col, 0, $col, self::H, $line);
        }

        return $img;
    }

    private function loadCover(?string $path)
    {
        if (! $path) {
            return null;
        }

        try {
            if (str_starts_with($path, 'http')) {
                $bytes = Http::timeout(5)->get($path)->body();
            } else {
                $bytes = Storage::disk('public')->get($path);
            }

            $cover = $bytes ? @imagecreatefromstring($bytes) : false;

            return $cover ?: null;
        } catch (\Throwable) {
            // カバーが読めなくても画像生成自体は成立させる
            return null;
        }
    }

    /**
     * カバーをアスペクト比を保ったままキャンバス全面にcover-cropで敷く。
     */
    private function drawCoverFilled($img, $cover): void
    {
        $cw = imagesx($cover);
        $ch = imagesy($cover);
        $scale = max(self::W / $cw, self::H / $ch);
        $srcW = (int) round(self::W / $scale);
        $srcH = (int) round(self::H / $scale);
        $srcX = (int) round(($cw - $srcW) / 2);
        $srcY = (int) round(($ch - $srcH) / 2);

        imagecopyresampled($img, $cover, 0, 0, $srcX, $srcY, self::W, self::H, $srcW, $srcH);
    }

    private function wordmark($img): void
    {
        imagefilledrectangle($img, self::MARGIN, 64, self::MARGIN + 10, 64 + 40, $this->color($img, self::ORANGE));
        $this->text($img, 'SUNO REVIEW', 30, self::MARGIN + 32, 64 + 33, self::WHITE);
    }

    private function footer($img): void
    {
        imagefilledrectangle($img, 0, self::H - 10, self::W, self::H, $this->color($img, self::ORANGE));
    }

    /**
     * 右下に大きくスコアを描く。「俺の曲◯◯点だった」の主役。
     */
    private function score($img, int $score): void
    {
        $numSize = 150;
        $num = (string) $score;

        [$numW] = $this->measure($num, $numSize);
        [$denomW] = $this->measure('/100', 38);

        $baseline = self::H - 88;
        $x = self::W - self::MARGIN - $numW - 16 - $denomW;

        $this->text($img, 'SCORE', 24, $x + 4, $baseline - $numSize - 26, self::GRAY_DIM);
        $this->text($img, $num, $numSize, $x, $baseline, self::ORANGE);
        $this->text($img, '/100', 38, $x + $numW + 16, $baseline, self::GRAY);
    }

    /**
     * 折返し付きテキスト。次の描画開始に使えるよう最終行のベースラインY( + 行の下余白)を返す。
     * 日本語は単語区切りが無いので1文字ずつ幅を測って折り返す。
     */
    private function paragraph($img, string $text, int $size, int $x, int $y, int $maxWidth, int $maxLines, array $rgb, int $lineHeight): int
    {
        $lines = [];
        $current = '';

        foreach (mb_str_split($text) as $char) {
            [$w] = $this->measure($current.$char, $size);
            if ($w > $maxWidth && $current !== '') {
                $lines[] = $current;
                $current = $char;
                if (count($lines) === $maxLines) {
                    break;
                }
            } else {
                $current .= $char;
            }
        }

        if (count($lines) < $maxLines && $current !== '') {
            $lines[] = $current;
        } elseif ($current !== '') {
            // 行数超過: 最終行を詰めて省略記号
            $last = $lines[$maxLines - 1];
            while ($last !== '' && $this->measure($last.'…', $size)[0] > $maxWidth) {
                $last = mb_substr($last, 0, -1);
            }
            $lines[$maxLines - 1] = $last.'…';
        }

        foreach ($lines as $line) {
            $y += $lineHeight;
            $this->text($img, $line, $size, $x, $y, $rgb);
        }

        return $y;
    }

    private function text($img, string $text, int $size, int $x, int $y, array $rgb): void
    {
        imagettftext($img, $size, 0, $x, $y, $this->color($img, $rgb), $this->font(), $text);
    }

    /** @return array{0: int, 1: int} [幅, 高さ] */
    private function measure(string $text, int $size): array
    {
        $box = imagettfbbox($size, 0, $this->font(), $text);

        return [abs($box[4] - $box[0]), abs($box[5] - $box[1])];
    }

    private function color($img, array $rgb): int
    {
        return imagecolorallocate($img, $rgb[0], $rgb[1], $rgb[2]);
    }

    private function font(): string
    {
        return resource_path('fonts/ZenKakuGothicNew-Bold.ttf');
    }

    private function png($img): string
    {
        ob_start();
        imagepng($img);
        imagedestroy($img);

        return ob_get_clean();
    }
}
