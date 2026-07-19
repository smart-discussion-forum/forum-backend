<?php

namespace App\Notifications;

use App\Models\Topic;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewTopicPosted extends Notification
{
    use Queueable;

    public function __construct(public Topic $topic)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'new_topic',
            'topic_id' => $this->topic->id,
            'group_id' => $this->topic->group_id,
            'title' => $this->topic->title,
            'message' => 'New topic posted: ' . $this->topic->title,
        ];
    }
}
