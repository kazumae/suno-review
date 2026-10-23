<?php

namespace Tests\Feature\Admin;

use App\Models\ReviewRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewRequestDateTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function requestReceivedAt(string $at): ReviewRequest
    {
        $this->travelTo($at);

        return ReviewRequest::create([
            'suno_url' => 'https://suno.com/s/vhAm4fua9zT8ThTr',
            'artist_url' => 'https://suno.com/@example',
            'title' => 'テスト曲',
            'status' => 'open',
        ]);
    }

    public function test_request_list_includes_the_received_date(): void
    {
        $admin = $this->admin();
        $this->requestReceivedAt('2026-10-22 11:41:50');

        $this->actingAs($admin)
            ->get(route('admin.requests.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Requests/Index')
                ->where('requests.0.created_at', '2026-10-22T11:41:50.000000Z')
            );
    }

    public function test_dashboard_recent_requests_include_the_received_date(): void
    {
        $admin = $this->admin();
        $this->requestReceivedAt('2026-10-22 11:41:50');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Dashboard')
                ->where('recentRequests.0.created_at', '2026-10-22T11:41:50.000000Z')
            );
    }
}
