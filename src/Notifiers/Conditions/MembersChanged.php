<?php

namespace DirectoryTree\Watchdog\Notifiers\Conditions;

class MembersChanged extends Changed
{
    /**
     * Check the member attribute.
     *
     * @var array
     */
    protected $attributes = ['member'];
}
