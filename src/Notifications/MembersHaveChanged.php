<?php

namespace DirectoryTree\Watchdog\Notifications;

use DirectoryTree\Watchdog\Dogs\WatchGroupMembers;
use Illuminate\Notifications\Messages\MailMessage;

class MembersHaveChanged extends Notification
{
    /**
     * Get the mail representation of the notification.
     *
     * @param WatchGroupMembers $watchdog
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(WatchGroupMembers $watchdog)
    {
        return (new MailMessage())
            ->subject($watchdog->getNotifiableSubject())
            ->markdown('watchdog::members-changed', [
                'watchdog' => $watchdog,
            ]);
    }
}
