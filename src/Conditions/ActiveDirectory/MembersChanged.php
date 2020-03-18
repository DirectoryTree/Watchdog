<?php

namespace DirectoryTree\Watchdog\Conditions\ActiveDirectory;

use DirectoryTree\Watchdog\Conditions\Changed;

class MembersChanged extends Changed
{
    /**
     * Check the member attribute.
     *
     * @var array
     */
    protected $attributes = ['member'];
}
