<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Notifications\PasswordHasChanged;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\PasswordChanged;

class WatchPasswordChanges extends Watchdog
{
    protected $conditions = [PasswordChanged::class];

    public function notification()
    {
        return PasswordHasChanged::class;
    }
}
