<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Post;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecommendationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_personalized_and_trending_topics(): void
    {
        $user = User::factory()->create();
        $group = Group::create([
            'name' => 'Math Group',
            'description' => 'A place for math discussions',
            'created_by' => $user->id,
        ]);
        $group->members()->attach($user->id, ['role' => 'Member', 'joined_at' => now()]);

        $mathTopic = Topic::create([
            'group_id' => $group->id,
            'created_by' => $user->id,
            'title' => 'Calculus revision questions',
            'category' => 'General',
        ]);

        $programmingTopic = Topic::create([
            'group_id' => $group->id,
            'created_by' => $user->id,
            'title' => 'Laravel API patterns',
            'category' => 'General',
        ]);

        Post::create([
            'topic_id' => $mathTopic->id,
            'user_id' => $user->id,
            'content' => 'Recent math discussion',
            'created_at' => now(),
        ]);

        Post::create([
            'topic_id' => $programmingTopic->id,
            'user_id' => $user->id,
            'content' => 'Recent programming discussion',
            'created_at' => now()->subDays(2),
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/recommendations');

        $response->assertOk()
            ->assertJsonStructure([
                'personalized' => [
                    '*' => [
                        'topic' => ['id', 'title', 'category'],
                        'reason',
                        'score',
                    ],
                ],
                'trending' => [
                    '*' => [
                        'topic' => ['id', 'title', 'category'],
                        'reason',
                        'score',
                    ],
                ],
            ])
            ->assertJsonFragment(['title' => 'Calculus revision questions'])
            ->assertJsonFragment(['title' => 'Laravel API patterns']);
    }
}
