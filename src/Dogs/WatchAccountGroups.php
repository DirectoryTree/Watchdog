<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Notifications\AccountGroupsHaveChanged;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\GroupsChanged;

class WatchAccountGroups extends Watchdog
{
    /**
     * The watchdog conditions.
     *
     * @var array
     */
    protected $conditions = [GroupsChanged::class];

    /**
     * Get the groups that have been added.
     *
     * @return \Illuminate\Support\Collection
     */
    public function added()
    {
        return $this->after->attribute('memberof')->diff(
            $this->before->attribute('memberof')
        );
    }

    /**
     * Get the groups that have been removed.
     *
     * @return \Illuminate\Support\Collection
     */
    public function removed()
    {
        return $this->before->attribute('memberof')->diff(
            $this->after->attribute('memberof')
        );
    }

    /**
     * The watchdog notification.
     *
     * @return string
     */
    public function notification()
    {
        return AccountGroupsHaveChanged::class;
    }
}
