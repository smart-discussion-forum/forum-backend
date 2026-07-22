<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QuizPublished extends Notification
{
    use Queueable;

    /**
     * Deliberately untyped: this depends on Pearl's Quiz model, which may
     * not exist yet on this branch. Once App\Models\Quiz lands, feel free
     * to add the type-hint back (public Quiz $quiz).
     */
    public function __construct(public $quiz)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'quiz_published',
            'quiz_id' => $this->quiz->id ?? null,
            'title' => $this->quiz->title ?? null,
            'message' => 'A new quiz has been published' . (isset($this->quiz->title) ? ": {$this->quiz->title}" : '.'),
        ];
    }
}
