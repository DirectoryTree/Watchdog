<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Notifications\LoginHasOccurred;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\NewLogin;

class WatchLogins extends Watchdog
{
    /**
     * The watchdog conditions.
     *
     * @var array
     */
    protected $conditions = [NewLogin::class];

    /**
     * The watchdog notification.
     *
     * @return string
     */
    public function notification()
    {
        return LoginHasOccurred::class;
    }
}
