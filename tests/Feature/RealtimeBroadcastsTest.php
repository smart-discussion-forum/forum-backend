<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Broadcast;
use RuntimeException;
use Tests\TestCase;

class RealtimeBroadcastsTest extends TestCase
{
    use RefreshDatabase;

    public function test_message_send_still_succeeds_when_broadcast_fails(): void
    {
        $user = User::factory()->create();
        $group = Group::create([
            'name' => 'Testing Group',
            'description' => 'For broadcast tests',
            'created_by' => $user->id,
        ]);
        $group->members()->attach($user->id, ['role' => 'Member', 'joined_at' => now()]);

        Broadcast::shouldReceive('connection')->andThrow(new RuntimeException('broadcast failed'));

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/messages/send', [
                'group_id' => $group->id,
                'content' => 'hello from chat',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('messages', [
            'group_id' => $group->id,
            'content' => 'hello from chat',
        ]);
    }

    public function test_topic_post_still_succeeds_when_broadcast_fails(): void
    {
        $user = User::factory()->create();
        $group = Group::create([
            'name' => 'Topic Group',
            'description' => 'For topic broadcast tests',
            'created_by' => $user->id,
        ]);
        $group->members()->attach($user->id, ['role' => 'Member', 'joined_at' => now()]);
        $topic = Topic::create([
            'group_id' => $group->id,
            'created_by' => $user->id,
            'title' => 'Topic title',
            'category' => 'General',
        ]);

        Broadcast::shouldReceive('connection')->andThrow(new RuntimeException('broadcast failed'));

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/topics/' . $topic->id . '/posts', [
                'content' => 'hello from topic',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('posts', [
            'topic_id' => $topic->id,
            'content' => 'hello from topic',
        ]);
    }
}
