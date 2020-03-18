<?php

namespace DirectoryTree\Watchdog\Conditions\ActiveDirectory;

use DirectoryTree\Watchdog\Conditions\Changed;

class PasswordChanged extends Changed
{
    protected $attributes = ['pwdlastset'];
}
