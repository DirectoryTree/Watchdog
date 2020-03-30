<?php

namespace DirectoryTree\Watchdog\Notifications;

use DirectoryTree\Watchdog\Watchdog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification as BaseNotification;

class Notification extends BaseNotification implements ShouldQueue
{
    use Queueable;

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
