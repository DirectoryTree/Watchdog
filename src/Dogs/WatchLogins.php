<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Conditions\ActiveDirectory\NewLogin;
use DirectoryTree\Watchdog\Notifications\LoginHasOccurred;
use DirectoryTree\Watchdog\Watchdog;

class WatchLogins extends Watchdog
{
    protected $conditions = [NewLogin::class];

    public function notification()
    {
        return LoginHasOccurred::class;
    }
}
