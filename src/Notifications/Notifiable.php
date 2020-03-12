<?php

namespace DirectoryTree\Watchdog\Notifications;

use Illuminate\Notifications\Notifiable as NotifiableTrait;

class Notifiable
{
    use NotifiableTrait;

    /**
     * @return string|null
     */
    public function routeNotificationForMail()
    {
        return config('scout.notifications.mail.to');
    }

    /**
     * @return string|null
     */
    public function routeNotificationForSlack()
    {
        return config('scout.notifications.slack.webhook_url');
    }

    public function getKey()
    {
        return static::class;
    }
}
