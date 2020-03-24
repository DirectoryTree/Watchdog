<?php

namespace DirectoryTree\Watchdog\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;

class PasswordHasChanged extends BaseNotification
{
    /**
     * Get the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail()
    {
        return (new MailMessage)
            ->subject($this->getMessageText())
            ->line($this->getMessageText());
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @return SlackMessage
     */
    public function toSlack()
    {
//        return (new SlackMessage)
//            ->content('')
//            ->attachment(function (SlackAttachment $attachment) {
//                $attachment
//                    ->title($this->getMessageText())
//                    ->content($this->getMonitor()->uptime_check_failure_reason)
//                    ->fallback($this->getMessageText())
//                    ->footer($this->getLocationDescription())
//                    ->timestamp(Carbon::now());
//            });
    }

    /**
     * Get the notification message text.
     *
     * @return string
     */
    protected function getMessageText()
    {
        return "Password Changed: {$this->object->name}";
    }
}
