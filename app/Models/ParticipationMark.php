<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParticipationMark extends Model
{
    protected $fillable = ['user_id', 'group_id', 'score'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Recalculate and persist a student's participation mark for a group
     * based on their discussion posts in that group.
     */
    public static function awardForUserInGroup(int $userId, int $groupId): ?self
    {
        $user = User::find($userId);
        if (! $user || $user->role !== \App\Enums\RoleEnum::Student) {
            return null;
        }

        $postCount = Post::where('user_id', $userId)
            ->whereHas('topic', fn ($query) => $query->where('group_id', $groupId))
            ->count();

        $score = min(100, round($postCount * 2.5, 2));

        return static::updateOrCreate(
            ['user_id' => $userId, 'group_id' => $groupId],
            ['score' => $score]
        );
    }
}
