<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\VerifyNewEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/profile')->assertOk();
    }

    public function test_name_is_updated_immediately(): void
    {
        $user = User::factory()->create(['email' => 'old@example.com']);

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => 'New Name',
            'email' => 'old@example.com',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect('/profile');

        $user->refresh();
        $this->assertSame('New Name', $user->name);
        $this->assertSame('old@example.com', $user->email);
        $this->assertNull($user->pending_email);
    }

    public function test_changing_email_is_held_until_verified(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'old@example.com']);

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => 'new@example.com',
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect('/profile');

        $user->refresh();
        // 認証が済むまで email（ログインID）は切り替えない
        $this->assertSame('old@example.com', $user->email);
        $this->assertSame('new@example.com', $user->pending_email);
        $this->assertNotNull($user->email_verified_at);

        // 確認メールは新アドレス宛の VerifyNewEmail として送られる
        Notification::assertSentTo(
            $user,
            VerifyNewEmail::class,
            fn (VerifyNewEmail $notification) => $notification->newEmail === 'new@example.com'
        );
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_reverting_email_clears_pending_change(): void
    {
        $user = User::factory()->create([
            'email' => 'old@example.com',
            'pending_email' => 'new@example.com',
        ]);

        $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => 'old@example.com',
        ])->assertSessionHasNoErrors();

        $this->assertNull($user->refresh()->pending_email);
    }

    public function test_pending_email_is_confirmed_via_signed_link(): void
    {
        $user = User::factory()->create([
            'email' => 'old@example.com',
            'pending_email' => 'new@example.com',
        ]);

        $url = URL::temporarySignedRoute('profile.email.verify', now()->addMinutes(60), [
            'id' => $user->id,
            'hash' => sha1('new@example.com'),
        ]);

        $this->actingAs($user)->get($url)->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertSame('new@example.com', $user->email);
        $this->assertNull($user->pending_email);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_email_change_is_rejected_with_invalid_hash(): void
    {
        $user = User::factory()->create([
            'email' => 'old@example.com',
            'pending_email' => 'new@example.com',
        ]);

        $url = URL::temporarySignedRoute('profile.email.verify', now()->addMinutes(60), [
            'id' => $user->id,
            'hash' => sha1('attacker@example.com'),
        ]);

        $this->actingAs($user)->get($url)->assertForbidden();
        $this->assertSame('old@example.com', $user->refresh()->email);
    }

    public function test_email_change_link_requires_valid_signature(): void
    {
        $user = User::factory()->create([
            'email' => 'old@example.com',
            'pending_email' => 'new@example.com',
        ]);

        // 署名クエリの無い生URLは signed ミドルウェアで弾かれる
        $url = "/profile/email/verify/{$user->id}/".sha1('new@example.com');

        $this->actingAs($user)->get($url)->assertForbidden();
        $this->assertSame('old@example.com', $user->refresh()->email);
    }

    public function test_user_cannot_confirm_another_users_email_change(): void
    {
        $target = User::factory()->create([
            'email' => 'old@example.com',
            'pending_email' => 'new@example.com',
        ]);
        $intruder = User::factory()->create();

        $url = URL::temporarySignedRoute('profile.email.verify', now()->addMinutes(60), [
            'id' => $target->id,
            'hash' => sha1('new@example.com'),
        ]);

        $this->actingAs($intruder)->get($url)->assertForbidden();
        $this->assertSame('old@example.com', $target->refresh()->email);
    }

    public function test_pending_email_verification_can_be_resent(): void
    {
        Notification::fake();

        $user = User::factory()->create(['pending_email' => 'new@example.com']);

        $this->actingAs($user)->post(route('profile.email.resend'))->assertSessionHasNoErrors();

        Notification::assertSentTo(
            $user,
            VerifyNewEmail::class,
            fn (VerifyNewEmail $notification) => $notification->newEmail === 'new@example.com'
        );
    }

    public function test_pending_email_change_can_be_cancelled(): void
    {
        $user = User::factory()->create(['pending_email' => 'new@example.com']);

        $this->actingAs($user)->delete(route('profile.email.cancel'))->assertSessionHasNoErrors();

        $this->assertNull($user->refresh()->pending_email);
    }

    public function test_change_notification_is_routed_to_the_pending_email(): void
    {
        $user = User::factory()->make(['email' => 'old@example.com']);

        // メール変更の確認通知だけは新アドレス宛に送る
        $this->assertSame(
            'new@example.com',
            $user->routeNotificationForMail(new VerifyNewEmail('new@example.com'))
        );

        // それ以外の通知（パスワードリセット等）は現在のメール宛
        $this->assertSame(
            'old@example.com',
            $user->routeNotificationForMail(new ResetPassword('token'))
        );
    }
}
