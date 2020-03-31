<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Notifications\AccountHasBeenEnabled;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\AccountEnabled;

class WatchAccountEnable extends Watchdog
{
    /**
     * The watchdog conditions.
     *
     * @var array
     */
    protected $conditions = [AccountEnabled::class];

    /**
     * The watchdog notification.
     *
     * @return string
     */
    public function notification()
    {
        return AccountHasBeenEnabled::class;
    }
}
