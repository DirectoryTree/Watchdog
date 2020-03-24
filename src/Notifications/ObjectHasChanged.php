<?php

namespace DirectoryTree\Watchdog\Notifications;

use DirectoryTree\Watchdog\Watchdog;
use Illuminate\Notifications\Messages\MailMessage;

class ObjectHasChanged extends BaseNotification
{
    /**
     * Get the mail representation of the notification.
     *
     * @param Watchdog $watchdog
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(Watchdog $watchdog)
    {
        return (new MailMessage)->markdown('watchdog::changed', [
            'watchdog' => $watchdog,
        ]);
    }
}
