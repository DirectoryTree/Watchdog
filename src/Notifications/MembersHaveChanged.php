<?php

namespace DirectoryTree\Watchdog\Notifications;

use DirectoryTree\Watchdog\Dogs\WatchMemberships;
use Illuminate\Notifications\Messages\MailMessage;

class MembersHaveChanged extends Notification
{
    /**
     * Get the mail representation of the notification.
     *
     * @param WatchMemberships $watchdog
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(WatchMemberships $watchdog)
    {
        return (new MailMessage())
            ->subject($this->getSubject($watchdog))
            ->markdown('watchdog::members-changed', [
                'watchdog' => $watchdog,
            ]);
    }

    /**
     * Get the subject for the watchdog notification.
     *
     * @param WatchMemberships $watchdog
     *
     * @return string
     */
    protected function getSubject(WatchMemberships $watchdog)
    {
        return "Group '{$watchdog->object()->name}' has had members changed";
    }
}
