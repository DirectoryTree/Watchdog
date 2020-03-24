<?php

namespace DirectoryTree\Watchdog\Notifications;

use DirectoryTree\Watchdog\LdapObject;
use Illuminate\Notifications\Notification;

class BaseNotification extends Notification
{
    /**
     * The LDAP object that has been changed.
     *
     * @var LdapObject
     */
    public $object;

    /**
     * Constructor.
     *
     * @param LdapObject $object
     */
    public function __construct(LdapObject $object)
    {
        $this->object = $object;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }
}
