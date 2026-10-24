<?php

namespace Tests\Feature\Admin;

use App\Models\ReviewRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewRequestArtistNameTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function reviewRequest(): ReviewRequest
    {
        return ReviewRequest::create([
            'suno_url' => 'https://suno.com/s/vhAm4fua9zT8ThTr',
            'artist_name' => 'テストアーティスト',
            'artist_url' => 'https://suno.com/@example',
            'title' => 'テスト曲',
            'status' => 'open',
        ]);
    }

    public function test_request_list_includes_the_artist_name(): void
    {
        $this->reviewRequest();

        $this->actingAs($this->admin())
            ->get(route('admin.requests.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Requests/Index')
                ->where('requests.0.artist_name', 'テストアーティスト')
            );
    }

    public function test_song_form_is_prefilled_with_the_artist_name_from_the_request(): void
    {
        $request = $this->reviewRequest();

        $this->actingAs($this->admin())
            ->get(route('admin.songs.create', ['request_id' => $request->id]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Songs/Create')
                ->where('prefill.artist_name', 'テストアーティスト')
                ->where('prefill.artist_url', 'https://suno.com/@example')
            );
    }
}
