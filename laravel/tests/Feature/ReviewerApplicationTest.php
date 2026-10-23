<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewerApplicationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'テスト太郎',
            'email' => 'applicant@gmail.com',
            'suno_id' => '@example',
            'motivation' => 'レビューを書いてみたいです。',
        ], $overrides);
    }

    // email:dns は実際に DNS を引くので、引けない環境では受理される側のテストを飛ばす
    private function requireDns(): void
    {
        if (! checkdnsrr('gmail.com', 'MX')) {
            $this->markTestSkipped('DNS を引けない環境のため');
        }
    }

    public function test_application_is_stored(): void
    {
        $this->requireDns();

        $this->post(route('applications.store'), $this->payload())
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('home'));

        $this->assertDatabaseHas('reviewer_applications', [
            'email' => 'applicant@gmail.com',
            'status' => 'pending',
        ]);
    }

    public function test_email_without_existing_domain_is_rejected(): void
    {
        // 脆弱性スキャナ(Acunetix)が既定で使うアドレス。.tst は実在しない TLD
        $this->post(route('applications.store'), $this->payload(['email' => 'sample@email.tst']))
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('reviewer_applications', 0);
    }

    public function test_applications_are_limited_to_5_per_hour_per_ip(): void
    {
        $this->requireDns();

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('applications.store'), $this->payload())->assertRedirect(route('home'));
        }

        $this->post(route('applications.store'), $this->payload())->assertTooManyRequests();

        $this->assertDatabaseCount('reviewer_applications', 5);
    }
}
