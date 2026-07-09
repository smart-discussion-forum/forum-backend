<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_register_with_multiple_groups(): void
    {
        $creator = User::factory()->create();
        $groupOne = Group::create([
            'name' => 'Group One',
            'description' => 'First group',
            'created_by' => $creator->id,
        ]);
        $groupTwo = Group::create([
            'name' => 'Group Two',
            'description' => 'Second group',
            'created_by' => $creator->id,
        ]);

        $response = $this->post('/register', [
            'name' => 'Alice Example',
            'email' => 'alice@example.com',
            'password' => 'password123',
            'role' => 'student',
            'group_ids' => [$groupOne->id, $groupTwo->id],
            'accepted_terms' => '1',
        ]);

        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'alice@example.com')->firstOrFail();

        $this->assertCount(2, $user->groups()->pluck('groups.id'));
        $this->assertTrue($user->groups()->where('groups.id', $groupOne->id)->exists());
        $this->assertTrue($user->groups()->where('groups.id', $groupTwo->id)->exists());
    }
}
