<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Notifications\AccountHasBeenDisabled;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\AccountDisabled;

class WatchAccountDisable extends Watchdog
{
    protected $conditions = [AccountDisabled::class];

    public function getName()
    {
        return trans('watchdog::watchdogs.accounts_disabled');
    }

    public function getKey()
    {
        return 'watchdog.accounts.disabled';
    }

    public function notification()
    {
        return AccountHasBeenDisabled::class;
    }
}
