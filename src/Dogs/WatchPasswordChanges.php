<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Ldap\TypeGuesser;
use DirectoryTree\Watchdog\Notifications\PasswordHasChanged;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\PasswordChanged;

class WatchPasswordChanges extends Watchdog
{
    protected $conditions = [
        PasswordChanged::class,
    ];

    public function bark()
    {
        // Here we will make sure to only send a notification on the event that the
        // object is a user and not a computer. Computer passwords are updated
        // automatically and often, and this will throw off our statistics.
        if ($this->object->type == TypeGuesser::TYPE_USER) {
            parent::bark();
        }
    }

    public function getName()
    {
        return trans('watchdog::watchdogs.passwords_changed');
    }

    public function getKey()
    {
        return 'watchdog.accounts.passwords';
    }

    public function getNotifiableSubject()
    {
        return "{$this->object->name} has had their password changed";
    }

    public function notification()
    {
        return PasswordHasChanged::class;
    }
}
