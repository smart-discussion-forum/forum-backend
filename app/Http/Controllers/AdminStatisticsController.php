<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminStatisticsController extends Controller
{
    /**
     * Overview of every group with aggregate activity metrics
     * (not per-student participation marks).
     */
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        $groups = Group::with('creator')
            ->withCount(['members', 'topics', 'messages'])
            ->orderBy('name')
            ->get()
            ->map(fn (Group $group) => $this->buildGroupStats($group));

        $payload = [
            'groups' => $groups,
        ];

        return $request->expectsJson()
            ? response()->json($payload)
            : view('admin.statistics.index', $payload);
    }

    /**
     * Detailed overall stats for a single group.
     */
    public function show(Request $request, int $id): View|\Illuminate\Http\JsonResponse
    {
        $group = Group::with('creator')
            ->withCount(['members', 'topics', 'messages'])
            ->findOrFail($id);

        $payload = [
            'stats' => $this->buildGroupStats($group),
        ];

        return $request->expectsJson()
            ? response()->json($payload)
            : view('admin.statistics.show', $payload);
    }

    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     description: ?string,
     *     created_by: ?string,
     *     member_count: int,
     *     topic_count: int,
     *     message_count: int,
     *     total_posts: int,
     *     posts_this_week: int,
     *     most_active_topic: ?array{id: int, title: string, posts_count: int},
     *     least_active_topic: ?array{id: int, title: string, posts_count: int},
     *     topics: \Illuminate\Support\Collection
     * }
     */
    private function buildGroupStats(Group $group): array
    {
        $topics = $group->topics()
            ->withCount('posts')
            ->orderBy('title')
            ->get();

        $totalPosts = (int) $topics->sum('posts_count');

        $postsThisWeek = Post::query()
            ->whereHas('topic', fn ($query) => $query->where('group_id', $group->id))
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $mostActive = $topics->sortByDesc('posts_count')->first();
        $leastActive = $topics->sortBy('posts_count')->first();

        return [
            'id' => $group->id,
            'name' => $group->name,
            'description' => $group->description,
            'created_by' => $group->creator?->name,
            'member_count' => (int) $group->members_count,
            'topic_count' => (int) $group->topics_count,
            'message_count' => (int) $group->messages_count,
            'total_posts' => $totalPosts,
            'posts_this_week' => $postsThisWeek,
            'most_active_topic' => $mostActive ? [
                'id' => $mostActive->id,
                'title' => $mostActive->title,
                'posts_count' => (int) $mostActive->posts_count,
            ] : null,
            'least_active_topic' => $leastActive ? [
                'id' => $leastActive->id,
                'title' => $leastActive->title,
                'posts_count' => (int) $leastActive->posts_count,
            ] : null,
            'topics' => $topics->map(fn ($topic) => [
                'id' => $topic->id,
                'title' => $topic->title,
                'category' => $topic->category,
                'posts_count' => (int) $topic->posts_count,
            ])->values(),
        ];
    }
}
