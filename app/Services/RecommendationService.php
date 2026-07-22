<?php

namespace App\Services;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Collection;

class RecommendationService
{
    /**
     * @return Collection<int, object{topic: Topic, reason: ?string, score: ?float}>
     */
    public function recommendedTopicsFor(User $user): Collection
    {
        return $this->personalizedTopicsFor($user);
    }

    /**
     * @return Collection<int, object{topic: Topic, reason: ?string, score: ?float}>
     */
    public function personalizedTopicsFor(User $user): Collection
    {
        $groupIds = $user->groups()->pluck('groups.id');

        return Topic::query()
            ->whereIn('group_id', $groupIds)
            ->withCount('posts')
            ->withMax('posts as latest_post_at', 'created_at')
            ->whereDoesntHave('posts', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderByDesc('latest_post_at')
            ->take(10)
            ->get()
            ->map(function (Topic $topic) {
                return (object) [
                    'topic' => $topic,
                    'reason' => 'Recent activity in your groups',
                    'score' => round((float) ($topic->posts_count + 1) * 0.5 + 1, 2),
                ];
            });
    }

    /**
     * @return Collection<int, object{topic: Topic, reason: ?string, score: ?float}>
     */
    public function trendingTopicsFor(int $days = 7): Collection
    {
        $cutoff = now()->subDays($days);

        return Topic::query()
            ->withCount(['posts as recent_posts_count' => function ($query) use ($cutoff) {
                $query->where('created_at', '>=', $cutoff);
            }])
            ->withCount('posts')
            ->withMax('posts as latest_post_at', 'created_at')
            ->whereHas('posts', function ($query) use ($cutoff) {
                $query->where('created_at', '>=', $cutoff);
            })
            ->orderByDesc('recent_posts_count')
            ->orderByDesc('latest_post_at')
            ->take(10)
            ->get()
            ->map(function (Topic $topic) {
                return (object) [
                    'topic' => $topic,
                    'reason' => 'Trending this week across the platform',
                    'score' => (float) $topic->recent_posts_count,
                ];
            });
    }

    public function combinedPayload(User $user): array
    {
        return [
            'personalized' => $this->personalizedTopicsFor($user)->values(),
            'trending' => $this->trendingTopicsFor()->values(),
        ];
    }
}
