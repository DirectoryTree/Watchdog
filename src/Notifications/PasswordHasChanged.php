<?php

namespace DirectoryTree\Watchdog\Notifications;

use DirectoryTree\Watchdog\Watchdog;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordHasChanged extends Notification
{
    /**
     * Get the mail representation of the notification.
     *
     * @param Watchdog $watchdog
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($watchdog)
    {
        return (new MailMessage)
            ->subject($this->getSubject($watchdog))
            ->markdown('watchdog::password-changed', [
                'watchdog' => $watchdog,
            ]);
    }

    /**
     * Get the subject for the watchdog notification.
     *
     * @param Watchdog $watchdog
     *
     * @return string
     */
    protected function getSubject(Watchdog $watchdog)
    {
        return "Account '{$watchdog->object()->name}' has had their password changed";
    }
}
