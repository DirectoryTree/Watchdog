<?php

namespace DirectoryTree\Watchdog\Conditions\ActiveDirectory;

use DirectoryTree\Watchdog\Conditions\Changed;

class GroupsChanged extends Changed
{
    /**
     * Check the member of attribute.
     *
     * @var array
     */
    protected $attributes = ['memberof'];
}
