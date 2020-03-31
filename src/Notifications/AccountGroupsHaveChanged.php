<?php

namespace DirectoryTree\Watchdog\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use DirectoryTree\Watchdog\Dogs\WatchAccountGroups;

class AccountGroupsHaveChanged extends Notification
{
    /**
     * Get the mail representation of the notification.
     *
     * @param WatchAccountGroups $watchdog
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(WatchAccountGroups $watchdog)
    {
        return (new MailMessage())
            ->subject($watchdog->getNotifiableSubject())
            ->markdown('watchdog::groups-changed', [
                'watchdog' => $watchdog,
            ]);
    }
}
