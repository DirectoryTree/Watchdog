<?php

namespace DirectoryTree\Watchdog\Conditions\ActiveDirectory;

use DirectoryTree\Watchdog\Conditions\Changed;

class AccountLocked extends Changed
{
    /**
     * Check the lockout time attribute.
     *
     * @var array
     */
    protected $attributes = ['lockouttime'];
}
