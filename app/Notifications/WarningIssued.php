<?php

namespace App\Notifications;

use App\Models\Warning;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WarningIssued extends Notification
{
    use Queueable;

    public function __construct(public Warning $warning)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'warning_issued',
            'warning_id' => $this->warning->Warning_id,
            'reason' => $this->warning->Reason,
            'message' => 'You have received a warning: ' . $this->warning->Reason,
        ];
    }
}
