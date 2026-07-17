<?php

namespace App\Events;

use App\Models\QuizAttempt;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuizAttemptSubmitted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public QuizAttempt $attempt;

    public function __construct(QuizAttempt $attempt)
    {
        $this->attempt = $attempt;
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('quiz.' . $this->attempt->quiz_id)];
    }

    public function broadcastAs(): string
    {
        return 'attempt.submitted';
    }

    public function broadcastWith(): array
    {
        $this->attempt->loadMissing('student:id,name');
        return [
            'student_name' => $this->attempt->student->name ?? 'Unknown',
            'score' => $this->attempt->Score,
            'auto_submitted' => $this->attempt->Auto_submitted,
        ];
    }
}