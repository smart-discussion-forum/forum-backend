<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('group.{groupId}', function ($user, $groupId) {
    return $user->groups()->where('groups.id', $groupId)->exists();
});

Broadcast::channel('quiz.{quizId}', function ($user, $quizId) {
    $quiz = \App\Models\Quiz::find($quizId);
    return $quiz && (int) $user->id === (int) $quiz->Lecturer_id;
});