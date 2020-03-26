<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Conditions\ActiveDirectory\AccountDisabled;
use DirectoryTree\Watchdog\Notifications\AccountHasBeenDisabled;
use DirectoryTree\Watchdog\Watchdog;

class WatchAccountDisable extends Watchdog
{
    protected $conditions = [AccountDisabled::class];

    public function notification()
    {
        return AccountHasBeenDisabled::class;
    }
}
