<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Notifications\AccountHasBeenLocked;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\AccountLocked;

class WatchAccountLockout extends Watchdog
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
