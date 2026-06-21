<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReviewerManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function reviewer(): User
    {
        return User::factory()->create(['role' => 'reviewer']);
    }

    private function member(): User
    {
        return User::factory()->create(['role' => 'member']);
    }

    // -------------------------------------------------------------------------
    // edit
    // -------------------------------------------------------------------------

    public function test_admin_can_view_edit_page_for_reviewer(): void
    {
        $admin = $this->admin();
        $reviewer = $this->reviewer();

        $this->actingAs($admin)
            ->get(route('admin.reviewers.edit', $reviewer))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Reviewers/Edit')
                ->where('reviewer.id', $reviewer->id)
            );
    }

    public function test_admin_can_view_edit_page_for_another_admin(): void
    {
        $admin = $this->admin();
        $target = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.reviewers.edit', $target))
            ->assertOk();
    }

    public function test_reviewer_cannot_access_edit_page(): void
    {
        $reviewer = $this->reviewer();
        $target = $this->reviewer();

        $this->actingAs($reviewer)
            ->get(route('admin.reviewers.edit', $target))
            ->assertForbidden();
    }

    public function test_member_cannot_access_edit_page(): void
    {
        $member = $this->member();
        $reviewer = $this->reviewer();

        $this->actingAs($member)
            ->get(route('admin.reviewers.edit', $reviewer))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_edit_page(): void
    {
        $reviewer = $this->reviewer();

        $this->get(route('admin.reviewers.edit', $reviewer))
            ->assertRedirect(route('login'));
    }

    public function test_edit_page_returns_404_for_plain_member(): void
    {
        $admin = $this->admin();
        $member = $this->member();

        $this->actingAs($admin)
            ->get(route('admin.reviewers.edit', $member))
            ->assertNotFound();
    }

    // -------------------------------------------------------------------------
    // update (email + role)
    // -------------------------------------------------------------------------

    public function test_admin_can_update_reviewer_email_and_role(): void
    {
        $admin = $this->admin();
        $reviewer = $this->reviewer();

        $this->actingAs($admin)->put(route('admin.reviewers.update', $reviewer), [
            'email' => 'new@example.com',
            'role' => 'admin',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $reviewer->refresh();
        $this->assertSame('new@example.com', $reviewer->email);
        $this->assertSame('admin', $reviewer->role);
    }

    public function test_email_is_updated_immediately_without_verification(): void
    {
        $admin = $this->admin();
        $reviewer = $this->reviewer();

        $this->actingAs($admin)->put(route('admin.reviewers.update', $reviewer), [
            'email' => 'direct@example.com',
            'role' => 'reviewer',
        ])->assertSessionHasNoErrors();

        $reviewer->refresh();
        $this->assertSame('direct@example.com', $reviewer->email);
        $this->assertNull($reviewer->pending_email);
    }

    public function test_pending_email_is_cleared_on_update(): void
    {
        $admin = $this->admin();
        $reviewer = $this->reviewer();
        $reviewer->pending_email = 'pending@example.com';
        $reviewer->save();

        $this->actingAs($admin)->put(route('admin.reviewers.update', $reviewer), [
            'email' => 'confirmed@example.com',
            'role' => 'reviewer',
        ])->assertSessionHasNoErrors();

        $this->assertNull($reviewer->refresh()->pending_email);
    }

    public function test_update_rejects_duplicate_email(): void
    {
        $admin = $this->admin();
        $reviewer = $this->reviewer();
        $other = $this->reviewer();

        $this->actingAs($admin)->put(route('admin.reviewers.update', $reviewer), [
            'email' => $other->email,
            'role' => 'reviewer',
        ])->assertSessionHasErrors('email');
    }

    public function test_update_allows_same_email_for_self(): void
    {
        $admin = $this->admin();
        $reviewer = $this->reviewer();

        $this->actingAs($admin)->put(route('admin.reviewers.update', $reviewer), [
            'email' => $reviewer->email,
            'role' => 'reviewer',
        ])->assertSessionHasNoErrors();
    }

    public function test_update_rejects_member_role(): void
    {
        $admin = $this->admin();
        $reviewer = $this->reviewer();

        $this->actingAs($admin)->put(route('admin.reviewers.update', $reviewer), [
            'email' => $reviewer->email,
            'role' => 'member',
        ])->assertSessionHasErrors('role');
    }

    public function test_reviewer_cannot_update_another_reviewer(): void
    {
        $reviewer = $this->reviewer();
        $target = $this->reviewer();

        $this->actingAs($reviewer)->put(route('admin.reviewers.update', $target), [
            'email' => 'hacked@example.com',
            'role' => 'reviewer',
        ])->assertForbidden();
    }

    public function test_update_returns_404_for_plain_member(): void
    {
        $admin = $this->admin();
        $member = $this->member();

        $this->actingAs($admin)->put(route('admin.reviewers.update', $member), [
            'email' => $member->email,
            'role' => 'reviewer',
        ])->assertNotFound();
    }

    // -------------------------------------------------------------------------
    // updatePassword
    // -------------------------------------------------------------------------

    public function test_admin_can_change_reviewer_password(): void
    {
        $admin = $this->admin();
        $reviewer = $this->reviewer();

        $this->actingAs($admin)->put(route('admin.reviewers.password.update', $reviewer), [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertTrue(Hash::check('newpassword123', $reviewer->refresh()->password));
    }

    public function test_password_change_does_not_require_current_password(): void
    {
        $admin = $this->admin();
        $reviewer = $this->reviewer();

        $this->actingAs($admin)->put(route('admin.reviewers.password.update', $reviewer), [
            'password' => 'absolutelynew1',
            'password_confirmation' => 'absolutelynew1',
        ])->assertSessionHasNoErrors();
    }

    public function test_password_confirmation_must_match(): void
    {
        $admin = $this->admin();
        $reviewer = $this->reviewer();

        $this->actingAs($admin)->put(route('admin.reviewers.password.update', $reviewer), [
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpass',
        ])->assertSessionHasErrors('password');
    }

    public function test_password_must_be_at_least_8_characters(): void
    {
        $admin = $this->admin();
        $reviewer = $this->reviewer();

        $this->actingAs($admin)->put(route('admin.reviewers.password.update', $reviewer), [
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');
    }

    public function test_reviewer_cannot_change_another_reviewers_password(): void
    {
        $reviewer = $this->reviewer();
        $target = $this->reviewer();

        $this->actingAs($reviewer)->put(route('admin.reviewers.password.update', $target), [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertForbidden();
    }

    public function test_password_update_returns_404_for_plain_member(): void
    {
        $admin = $this->admin();
        $member = $this->member();

        $this->actingAs($admin)->put(route('admin.reviewers.password.update', $member), [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertNotFound();
    }
}
