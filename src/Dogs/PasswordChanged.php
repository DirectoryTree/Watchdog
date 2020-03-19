<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\LdapObject;
use DirectoryTree\Watchdog\Notifications\PasswordHasChanged;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\PasswordChanged as Condition;

class PasswordChanged extends Watchdog
{
    protected $conditions = [Condition::class];

    public function notification(LdapObject $object)
    {
        return new PasswordHasChanged($object);
    }
}
