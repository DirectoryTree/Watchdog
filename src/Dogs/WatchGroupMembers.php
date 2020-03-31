<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Notifications\MembersHaveChanged;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\MembersChanged;

class WatchGroupMembers extends Watchdog
{
    /**
     * The watchdog conditions.
     *
     * @var array
     */
    protected $conditions = [MembersChanged::class];

    /**
     * Get the members that have been added.
     *
     * @return \Illuminate\Support\Collection
     */
    public function added()
    {
        return $this->after->attribute('member')->diff(
            $this->before->attribute('member')
        );
    }

    /**
     * Get the members that have been removed.
     *
     * @return \Illuminate\Support\Collection
     */
    public function removed()
    {
        return $this->before->attribute('member')->diff(
            $this->after->attribute('member')
        );
    }

    /**
     * The watchdog notification.
     *
     * @return string
     */
    public function notification()
    {
        return MembersHaveChanged::class;
    }
}
