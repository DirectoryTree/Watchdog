<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Conditions\ActiveDirectory\NewLogin;
use DirectoryTree\Watchdog\Watchdog;

class Logins extends Watchdog
{
    protected $conditions = [NewLogin::class];
}
