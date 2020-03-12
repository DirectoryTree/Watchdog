<?php

namespace DirectoryTree\Watchdog\Notifiers\Conditions;

class GroupsChanged extends Changed
{
    /**
     * Check the member of attribute.
     *
     * @var array
     */
    protected $attributes = ['memberof'];
}
