<?php

namespace DirectoryTree\Watchdog\Notifications;

use DirectoryTree\Watchdog\Dogs\WatchMembers;
use Illuminate\Notifications\Messages\MailMessage;

class MembersHaveChanged extends Notification
{
    /**
     * Get the mail representation of the notification.
     *
     * @param WatchMembers $watchdog
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(WatchMembers $watchdog)
    {
        return (new MailMessage)
            ->subject($this->getSubject($watchdog))
            ->markdown('watchdog::members-changed', [
                'watchdog' => $watchdog,
            ]);
    }

    /**
     * Get the subject for the watchdog notification.
     *
     * @param WatchMembers $watchdog
     *
     * @return string
     */
    protected function getSubject(WatchMembers $watchdog)
    {
        return "{$watchdog->object()->name} has had members changed";
    }
}
