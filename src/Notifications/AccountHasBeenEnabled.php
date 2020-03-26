<?php

namespace DirectoryTree\Watchdog\Notifications;

use DirectoryTree\Watchdog\Watchdog;
use Illuminate\Notifications\Messages\MailMessage;

class AccountHasBeenEnabled extends Notification
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
        return (new MailMessage)
            ->subject($this->getSubject($watchdog))
            ->markdown('watchdog::account-enabled', [
                'watchdog' => $watchdog,
            ]);
    }

    /**
     * Get the subject for the watchdog.
     *
     * @param Watchdog $watchdog
     *
     * @return string
     */
    protected function getSubject(Watchdog $watchdog)
    {
        return "Account {$watchdog->object()->name} has been enabled";
    }
}
