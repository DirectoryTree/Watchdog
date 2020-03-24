<?php

namespace DirectoryTree\Watchdog\Conditions\ActiveDirectory;

use DirectoryTree\Watchdog\Conditions\Changed;

class NewLogin extends Changed
{
    /**
     * Check the lastlogon attribute.
     *
     * @var array
     */
    protected $attributes = ['lastlogon'];
}
