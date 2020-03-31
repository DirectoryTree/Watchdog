<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Notifications\LoginHasOccurred;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\NewLogin;

class WatchLogins extends Watchdog
{
    protected $conditions = [NewLogin::class];

    public function getName()
    {
        return trans('watchdog::watchdogs.new_logins');
    }

    public function getKey()
    {
        return 'watchdog.accounts.logins';
    }

    public function getNotifiableSubject()
    {
        return "Account [{$this->object->name}] has a new login";
    }

    public function notification()
    {
        return LoginHasOccurred::class;
    }
}
