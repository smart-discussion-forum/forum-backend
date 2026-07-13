<?php

namespace App\Notifications;

use App\Models\Blacklist;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserBlacklisted extends Notification
{
    use Queueable;

    public function __construct(public Blacklist $blacklist)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'user_blacklisted',
            'blacklist_id' => $this->blacklist->Blacklist_id,
            'reason' => $this->blacklist->Reason,
            'expires_at' => optional($this->blacklist->Expires_at)->toDateTimeString(),
            'message' => 'Your account has been blacklisted: ' . $this->blacklist->Reason,
        ];
    }
}
