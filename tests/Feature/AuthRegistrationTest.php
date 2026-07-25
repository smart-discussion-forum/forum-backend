<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_register_without_selecting_groups(): void
    {
        $creator = User::factory()->create();
        Group::create([
            'name' => 'Group One',
            'description' => 'First group',
            'created_by' => $creator->id,
        ]);

        $response = $this->post('/register', [
            'name' => 'Alice Example',
            'email' => 'alice@example.com',
            'password' => 'password123',
            'role' => 'student',
            'accepted_terms' => '1',
        ]);

        $response->assertRedirect('/groups');

        $user = User::where('email', 'alice@example.com')->firstOrFail();

        $this->assertCount(0, $user->groups()->pluck('groups.id'));
    }

    public function test_authenticated_users_can_join_and_leave_groups(): void
    {
        $creator = User::factory()->create();
        $group = Group::create([
            'name' => 'Group One',
            'description' => 'First group',
            'created_by' => $creator->id,
        ]);
        $user = User::factory()->create();

        $this->actingAs($user);

        $joinResponse = $this->post('/groups/' . $group->id . '/join');
        $joinResponse->assertRedirect();
        $this->assertTrue($user->fresh()->groups()->where('groups.id', $group->id)->exists());

        $leaveResponse = $this->post('/groups/' . $group->id . '/leave');
        $leaveResponse->assertRedirect();
        $this->assertFalse($user->fresh()->groups()->where('groups.id', $group->id)->exists());
    }

    public function test_user_can_reset_password_directly_from_forgot_password_page(): void
    {
        $user = User::factory()->create([
            'email' => 'student@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('status', 'Password updated. You can now log in with your new password.');

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'new-password',
        ])->assertRedirect('/dashboard');
    }
}
