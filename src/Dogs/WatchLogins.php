<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Notifications\LoginHasOccurred;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\NewLogin;

class WatchLogins extends Watchdog
{
    protected $conditions = [NewLogin::class];

    public function notification()
    {
        return LoginHasOccurred::class;
    }
}
