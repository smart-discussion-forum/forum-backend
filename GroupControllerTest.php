<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * NOTE: These tests require Noerine's `group_members` migration to exist
 * before they can run against a real database (RefreshDatabase will fail
 * without it). Written against the pivot columns already referenced in
 * Group::members() / User::groups() -- 'role' and 'joined_at'.
 */
class GroupControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_lecturer_can_create_a_group(): void
    {
        $lecturer = User::factory()->create(['role' => RoleEnum::Lecturer]);

        $response = $this->actingAs($lecturer)->post('/groups', [
            'name' => 'CS301 Study Group',
            'description' => 'Weekly discussion group',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('groups', ['name' => 'CS301 Study Group']);
    }

    public function test_admin_can_create_a_group(): void
    {
        $admin = User::factory()->create(['role' => RoleEnum::Admin]);

        $response = $this->actingAs($admin)->post('/groups', [
            'name' => 'Admin Announcements',
        ]);

        $response->assertStatus(201);
    }

    public function test_student_cannot_create_a_group(): void
    {
        $student = User::factory()->create(['role' => RoleEnum::Student]);

        $response = $this->actingAs($student)->post('/groups', [
            'name' => 'Not Allowed',
        ]);

        $response->assertStatus(403);
    }

    public function test_student_can_join_a_group(): void
    {
        $student = User::factory()->create(['role' => RoleEnum::Student]);
        $lecturer = User::factory()->create(['role' => RoleEnum::Lecturer]);
        $group = Group::create([
            'name' => 'Joinable Group',
            'created_by' => $lecturer->id,
        ]);

        $response = $this->actingAs($student)->post("/groups/{$group->id}/join");

        $response->assertStatus(200);
        $this->assertTrue($group->fresh()->hasMember($student));
    }

    public function test_lecturer_cannot_join_a_group_via_student_only_endpoint(): void
    {
        $lecturer = User::factory()->create(['role' => RoleEnum::Lecturer]);
        $group = Group::create([
            'name' => 'Restricted Join Group',
            'created_by' => $lecturer->id,
        ]);

        $response = $this->actingAs($lecturer)->post("/groups/{$group->id}/join");

        $response->assertStatus(403);
    }

    public function test_student_cannot_join_the_same_group_twice(): void
    {
        $student = User::factory()->create(['role' => RoleEnum::Student]);
        $lecturer = User::factory()->create(['role' => RoleEnum::Lecturer]);
        $group = Group::create([
            'name' => 'Duplicate Join Group',
            'created_by' => $lecturer->id,
        ]);

        $this->actingAs($student)->post("/groups/{$group->id}/join");
        $response = $this->actingAs($student)->post("/groups/{$group->id}/join");

        $response->assertStatus(409);
    }

    public function test_member_can_leave_a_group(): void
    {
        $student = User::factory()->create(['role' => RoleEnum::Student]);
        $lecturer = User::factory()->create(['role' => RoleEnum::Lecturer]);
        $group = Group::create([
            'name' => 'Leavable Group',
            'created_by' => $lecturer->id,
        ]);
        $group->members()->attach($student->id, ['role' => 'Member', 'joined_at' => now()]);

        $response = $this->actingAs($student)->post("/groups/{$group->id}/leave");

        $response->assertStatus(200);
        $this->assertFalse($group->fresh()->hasMember($student));
    }

    public function test_user_can_list_groups_they_belong_to(): void
    {
        $student = User::factory()->create(['role' => RoleEnum::Student]);
        $lecturer = User::factory()->create(['role' => RoleEnum::Lecturer]);
        $group = Group::create([
            'name' => 'Listed Group',
            'created_by' => $lecturer->id,
        ]);
        $group->members()->attach($student->id, ['role' => 'Member', 'joined_at' => now()]);

        $response = $this->actingAs($student)->get('/groups');

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Listed Group']);
    }
}
