<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Notifications\AccountHasBeenDisabled;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\AccountDisabled;

class WatchAccountDisable extends Watchdog
{
    /**
     * The watchdog conditions.
     *
     * @var array
     */
    protected $conditions = [AccountDisabled::class];

    /**
     * The watchdog notification.
     *
     * @return string
     */
    public function notification()
    {
        return AccountHasBeenDisabled::class;
    }
}
