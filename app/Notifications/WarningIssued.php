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
        $isAuto = $this->warning->Source === Warning::SOURCE_AUTO_INACTIVITY;
        $label = $isAuto ? 'Inactivity warning' : 'Warning';

        return [
            'type' => 'warning_issued',
            'source' => $this->warning->Source,
            'warning_id' => $this->warning->Warning_id,
            'reason' => $this->warning->Reason,
            'message' => "{$label}: {$this->warning->Reason}",
        ];
    }
}
