<?php

namespace DirectoryTree\Watchdog\Notifications;

use DirectoryTree\Watchdog\Watchdog;
use Illuminate\Notifications\Notification as BaseNotification;

class Notification extends BaseNotification
{
    /**
     * The watchdog notification delivery channels.
     *
     * @param Watchdog $watchdog
     *
     * @return array
     */
    public function via(Watchdog $watchdog)
    {
        return $watchdog->channels();
    }
}
