<?php

namespace DirectoryTree\Watchdog\Conditions\ActiveDirectory;

use DirectoryTree\Watchdog\Conditions\Changed;

class PasswordChanged extends Changed
{
    /**
     * Check the pwdlastset attribute.
     *
     * @var array
     */
    protected $attributes = ['pwdlastset'];
}
