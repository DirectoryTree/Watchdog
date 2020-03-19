<?php

namespace DirectoryTree\Watchdog\Notifications;

use Illuminate\Notifications\Notification;

class ObjectChanged extends Notification
{
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return config('watchdog.watchdogs.'.static::class);
    }
}
