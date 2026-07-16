<?php

namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewPostCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Post $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('group.'.$this->post->topic->group_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'post.created';
    }

    public function broadcastWith(): array
    {
        $this->post->loadMissing('user:id,name', 'topic:id,group_id,title');

        return [
            'id' => $this->post->id,
            'topic_id' => $this->post->topic_id,
            'group_id' => $this->post->topic->group_id,
            'content' => $this->post->content,
            'created_at' => $this->post->created_at?->toIso8601String(),
            'user' => [
                'id' => $this->post->user->id,
                'name' => $this->post->user->name,
            ],
        ];
    }
}