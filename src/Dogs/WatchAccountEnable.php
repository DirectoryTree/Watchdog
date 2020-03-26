<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Conditions\ActiveDirectory\AccountEnabled;
use DirectoryTree\Watchdog\Notifications\AccountHasBeenEnabled;
use DirectoryTree\Watchdog\Watchdog;

class WatchAccountEnable extends Watchdog
{
    protected $conditions = [AccountEnabled::class];

    public function notification()
    {
        return AccountHasBeenEnabled::class;
    }
}
