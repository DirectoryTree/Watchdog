<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Notifications\AccountHasBeenLocked;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\AccountLocked;

class WatchAccountLockout
{
    /**
     * The watchdog conditions.
     *
     * @var array
     */
    protected $conditions = [AccountLocked::class];

    /**
     * The watchdog notification.
     *
     * @return string
     */
    public function notification()
    {
        return AccountHasBeenLocked::class;
    }
}
