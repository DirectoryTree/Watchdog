<?php

namespace DirectoryTree\Watchdog\Dogs;

use DirectoryTree\Watchdog\Watchdog;
use DirectoryTree\Watchdog\Notifications\AccountGroupsHaveChanged;
use DirectoryTree\Watchdog\Conditions\ActiveDirectory\GroupsChanged;

class WatchAccountGroups extends Watchdog
{
    protected $conditions = [GroupsChanged::class];

    public function added()
    {
        return $this->after->attribute('memberof')->diff(
            $this->before->attribute('memberof')
        );
    }

    public function removed()
    {
        return $this->before->attribute('memberof')->diff(
            $this->after->attribute('memberof')
        );
    }

    public function getName()
    {
        return trans('watchdog::watchdogs.account_groups_changed');
    }

    public function getKey()
    {
        return 'watchdog.accounts.groups';
    }

    public function getNotifiableSubject()
    {
        return "Account [{$this->object->name}] has had their groups changed";
    }

    public function notification()
    {
        return AccountGroupsHaveChanged::class;
    }
}
