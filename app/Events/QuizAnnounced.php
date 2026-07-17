<?php

namespace App\Events;

use App\Models\Quiz;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuizAnnounced implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Quiz $quiz;

    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz;
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('group.' . $this->quiz->group_id)];
    }

    public function broadcastAs(): string
    {
        return 'quiz.announced';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->quiz->quiz_id,
            'title' => $this->quiz->title,
            'group_id' => $this->quiz->group_id,
            'start_time' => $this->quiz->start_time?->toIso8601String(),
            'status' => $this->quiz->status,
        ];
    }
}