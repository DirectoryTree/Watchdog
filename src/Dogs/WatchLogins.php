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
        return trans('watchdog::new_logins');
    }

    public function getKey()
    {
        return 'watchdog.accounts.logins';
    }

    public function notification()
    {
        return LoginHasOccurred::class;
    }
}
