<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Group;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function makeUser(RoleEnum $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    // --- STORE (Lecturer/Admin only) ---

    public function test_lecturer_can_create_group(): void
    {
        $lecturer = $this->makeUser(RoleEnum::Lecturer);

        $response = $this->actingAs($lecturer)->post('/groups', [
            'name' => 'Algorithms Study Group',
            'description' => 'Weekly problem solving',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('groups', ['name' => 'Algorithms Study Group']);
        $this->assertDatabaseHas('group_members', [
            'user_id' => $lecturer->id,
            'role' => 'Moderator',
        ]);
    }

    public function test_admin_can_create_group(): void
    {
        $admin = $this->makeUser(RoleEnum::Admin);

        $response = $this->actingAs($admin)->post('/groups', [
            'name' => 'Admin Group',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('groups', ['name' => 'Admin Group']);
    }

    public function test_student_cannot_create_group(): void
    {
        $student = $this->makeUser(RoleEnum::Student);

        $response = $this->actingAs($student)->post('/groups', [
            'name' => 'Not Allowed',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('groups', ['name' => 'Not Allowed']);
    }

    public function test_guest_cannot_create_group(): void
    {
        $response = $this->post('/groups', ['name' => 'No Auth']);

        $response->assertRedirect('/login');
    }

    public function test_store_requires_a_name(): void
    {
        $lecturer = $this->makeUser(RoleEnum::Lecturer);

        $response = $this->actingAs($lecturer)->post('/groups', []);

        $response->assertSessionHasErrors('name');
    }

    // --- INDEX ---

    public function test_index_lists_only_groups_the_user_belongs_to(): void
    {
        $user = $this->makeUser(RoleEnum::Student);
        $otherUser = $this->makeUser(RoleEnum::Student);

        $myGroup = Group::create([
            'name' => 'My Group',
            'created_by' => $user->id,
        ]);
        $myGroup->members()->attach($user->id, ['role' => 'Member', 'joined_at' => now()]);

        $notMyGroup = Group::create([
            'name' => 'Not My Group',
            'created_by' => $otherUser->id,
        ]);
        $notMyGroup->members()->attach($otherUser->id, ['role' => 'Member', 'joined_at' => now()]);

        $response = $this->actingAs($user)->get('/groups');

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'My Group']);
        $response->assertJsonMissing(['name' => 'Not My Group']);
    }

    // --- SHOW ---

    public function test_show_returns_group_with_members_and_topics(): void
    {
        $lecturer = $this->makeUser(RoleEnum::Lecturer);
        $student = $this->makeUser(RoleEnum::Student);

        $group = Group::create([
            'name' => 'Physics Group',
            'created_by' => $lecturer->id,
        ]);
        $group->members()->attach($lecturer->id, ['role' => 'Moderator', 'joined_at' => now()]);
        $group->members()->attach($student->id, ['role' => 'Member', 'joined_at' => now()]);

        Topic::create([
            'group_id' => $group->id,
            'created_by' => $lecturer->id,
            'title' => 'Kinematics',
            'category' => 'Mechanics',
        ]);

        $response = $this->actingAs($student)->get("/groups/{$group->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Kinematics']);
        $response->assertJsonCount(2, 'members');
    }

    public function test_show_returns_404_for_nonexistent_group(): void
    {
        $user = $this->makeUser(RoleEnum::Student);

        $response = $this->actingAs($user)->get('/groups/9999');

        $response->assertStatus(404);
    }

    // --- JOIN (Student only) ---

    public function test_student_can_join_group(): void
    {
        $lecturer = $this->makeUser(RoleEnum::Lecturer);
        $student = $this->makeUser(RoleEnum::Student);

        $group = Group::create(['name' => 'Chemistry Group', 'created_by' => $lecturer->id]);

        $response = $this->actingAs($student)->post("/groups/{$group->id}/join");

        $response->assertStatus(200);
        $this->assertDatabaseHas('group_members', [
            'user_id' => $student->id,
            'group_id' => $group->id,
        ]);
    }

    public function test_lecturer_cannot_join_via_student_endpoint(): void
    {
        $lecturer = $this->makeUser(RoleEnum::Lecturer);
        $group = Group::create(['name' => 'Biology Group', 'created_by' => $lecturer->id]);

        $response = $this->actingAs($lecturer)->post("/groups/{$group->id}/join");

        $response->assertStatus(403);
    }

    public function test_student_cannot_join_group_twice(): void
    {
        $lecturer = $this->makeUser(RoleEnum::Lecturer);
        $student = $this->makeUser(RoleEnum::Student);

        $group = Group::create(['name' => 'Math Group', 'created_by' => $lecturer->id]);
        $group->members()->attach($student->id, ['role' => 'Member', 'joined_at' => now()]);

        $response = $this->actingAs($student)->post("/groups/{$group->id}/join");

        $response->assertStatus(409);
        $this->assertDatabaseCount('group_members', 1);
    }

    // --- LEAVE ---

    public function test_member_can_leave_group(): void
    {
        $lecturer = $this->makeUser(RoleEnum::Lecturer);
        $student = $this->makeUser(RoleEnum::Student);

        $group = Group::create(['name' => 'History Group', 'created_by' => $lecturer->id]);
        $group->members()->attach($student->id, ['role' => 'Member', 'joined_at' => now()]);

        $response = $this->actingAs($student)->post("/groups/{$group->id}/leave");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('group_members', [
            'user_id' => $student->id,
            'group_id' => $group->id,
        ]);
    }

    public function test_non_member_cannot_leave_group(): void
    {
        $lecturer = $this->makeUser(RoleEnum::Lecturer);
        $student = $this->makeUser(RoleEnum::Student);

        $group = Group::create(['name' => 'Art Group', 'created_by' => $lecturer->id]);

        $response = $this->actingAs($student)->post("/groups/{$group->id}/leave");

        $response->assertStatus(409);
    }
}
