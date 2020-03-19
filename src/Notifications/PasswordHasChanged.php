<?php

namespace DirectoryTree\Watchdog\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class PasswordHasChanged extends BaseNotification
{
    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        //
    }
}
