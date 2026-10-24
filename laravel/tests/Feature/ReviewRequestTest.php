<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ReviewRequestTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'suno_url' => 'https://suno.com/s/vhAm4fua9zT8ThTr',
            'artist_name' => 'テストアーティスト',
            'artist_url' => 'https://suno.com/@example',
            'youtube_url' => 'https://youtu.be/15B4WbRM3ZE',
            'title' => 'テスト曲',
            'genre' => 'J-Pop',
            'note' => 'よろしくお願いします。',
        ], $overrides);
    }

    public function test_request_is_stored(): void
    {
        $this->post(route('requests.store'), $this->payload())
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('home'));

        $this->assertDatabaseHas('review_requests', [
            'suno_url' => 'https://suno.com/s/vhAm4fua9zT8ThTr',
            'artist_name' => 'テストアーティスト',
            'status' => 'open',
        ]);
    }

    public static function blankArtistNames(): array
    {
        return [
            '未送信' => [null],
            '空文字' => [''],
            '空白だけ' => ['   '],
        ];
    }

    #[DataProvider('blankArtistNames')]
    public function test_artist_name_is_required(?string $name): void
    {
        $payload = $this->payload(['artist_name' => $name]);
        if ($name === null) {
            unset($payload['artist_name']);
        }

        $this->post(route('requests.store'), $payload)
            ->assertSessionHasErrors('artist_name');

        $this->assertDatabaseCount('review_requests', 0);
    }

    public static function sunoUrls(): array
    {
        return [
            '共有リンク' => ['https://suno.com/s/vhAm4fua9zT8ThTr'],
            '楽曲ページ' => ['https://suno.com/song/480747dd-5726-483a-a4a5-90174f0e8337?sh=VlCUxLWt'],
            '旧ドメイン' => ['https://app.suno.ai/song/480747dd-5726-483a-a4a5-90174f0e8337'],
        ];
    }

    #[DataProvider('sunoUrls')]
    public function test_suno_urls_are_accepted(string $url): void
    {
        $this->post(route('requests.store'), $this->payload(['suno_url' => $url]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('review_requests', ['suno_url' => $url]);
    }

    public static function nonSunoUrls(): array
    {
        return [
            'スキャナが送ってきた形' => ['https://ithvpdjv.wou'],
            '別ドメイン' => ['https://example.com/song/1'],
            'SUNO に似せたドメイン' => ['https://suno.com.example.net/song/1'],
            '後ろだけ一致するドメイン' => ['https://notsuno.com/song/1'],
            'ユーザー情報で偽装' => ['https://suno.com@example.net/song/1'],
            'http(s) 以外' => ['ftp://suno.com/song/1'],
        ];
    }

    #[DataProvider('nonSunoUrls')]
    public function test_non_suno_urls_are_rejected(string $url): void
    {
        $this->post(route('requests.store'), $this->payload(['suno_url' => $url]))
            ->assertSessionHasErrors('suno_url');

        $this->assertDatabaseCount('review_requests', 0);
    }

    public function test_youtube_url_must_be_youtube(): void
    {
        $this->post(route('requests.store'), $this->payload(['youtube_url' => 'https://xviewmfq.sgx']))
            ->assertSessionHasErrors('youtube_url');

        $this->post(route('requests.store'), $this->payload(['youtube_url' => 'https://www.youtube.com/watch?v=dOvjJnG6L2M']))
            ->assertSessionHasNoErrors();
    }

    public function test_artist_url_must_be_http_or_https(): void
    {
        $this->post(route('requests.store'), $this->payload(['artist_url' => 'javascript://example.com/%0Aalert(1)']))
            ->assertSessionHasErrors('artist_url');

        $this->assertDatabaseCount('review_requests', 0);
    }

    public function test_requests_are_limited_to_10_per_hour_per_ip(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post(route('requests.store'), $this->payload())->assertRedirect(route('home'));
        }

        $this->post(route('requests.store'), $this->payload())
            ->assertTooManyRequests()
            ->assertSee('しばらく時間をおいてから、もう一度お試しください。');

        $this->assertDatabaseCount('review_requests', 10);
    }
}
